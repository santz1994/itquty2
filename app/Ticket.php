<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Ticket extends Model
{
  protected $fillable = [
    'user_id', 'location_id', 'ticket_status_id', 'ticket_type_id', 
    'ticket_priority_id', 'subject', 'description', 'ticket_code',
    'assigned_to', 'assigned_at', 'assignment_type', 'sla_due',
    'first_response_at', 'resolved_at', 'asset_id'
  ];

  protected $dates = [
    'assigned_at', 'sla_due', 'first_response_at', 'resolved_at', 'closed'
  ];

  protected static function boot()
  {
    parent::boot();
    
    static::creating(function ($ticket) {
      $ticket->ticket_code = self::generateTicketCode();
      $ticket->sla_due = self::calculateSLADue($ticket->ticket_priority_id);
    });
  }

  public static function generateTicketCode()
  {
    $prefix = 'TKT';
    $date = now()->format('Ymd');
    $lastTicket = self::whereDate('created_at', today())
                     ->orderBy('id', 'desc')
                     ->first();
    
    $sequence = $lastTicket ? 
                (int)substr($lastTicket->ticket_code, -3) + 1 : 1;
    
    return $prefix . '-' . $date . '-' . str_pad($sequence, 3, '0', STR_PAD_LEFT);
  }

  public static function calculateSLADue($priorityId)
  {
    $slaHours = [
      1 => 4,   // Urgent < 4 hours
      2 => 24,  // High < 1 day
      3 => 72,  // Medium < 3 days
      4 => 168, // Low < 1 week
    ];
    
    return now()->addHours($slaHours[$priorityId] ?? 72);
  }

  // Relationships
  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function assignedTo()
  {
    return $this->belongsTo(User::class, 'assigned_to');
  }

  public function location()
  {
    return $this->belongsTo(Location::class);
  }

  public function asset()
  {
    return $this->belongsTo(Asset::class);
  }

  public function ticket_status()
  {
    return $this->belongsTo(TicketsStatus::class);
  }

  public function ticket_priority()
  {
    return $this->belongsTo(TicketsPriority::class);
  }

  public function ticket_type()
  {
    return $this->belongsTo(TicketsType::class);
  }

  public function ticket_entries()
  {
    return $this->hasMany(TicketsEntry::class);
  }

  public function daily_activities()
  {
    return $this->hasMany(DailyActivity::class);
  }

  // Scopes
  public function scopeUnassigned($query)
  {
    return $query->whereNull('assigned_to');
  }

  public function scopeOverdue($query)
  {
    return $query->where('sla_due', '<', now())
                 ->whereNotIn('ticket_status_id', [3, 4]); // Not closed/resolved
  }

  public function scopeNearDeadline($query, $hours = 2)
  {
    return $query->where('sla_due', '<=', now()->addHours($hours))
                 ->where('sla_due', '>', now())
                 ->whereNotIn('ticket_status_id', [3, 4]);
  }

  public function scopeByStatus($query, $statusId)
  {
    return $query->where('ticket_status_id', $statusId);
  }

  public function scopeByPriority($query, $priorityId)
  {
    return $query->where('ticket_priority_id', $priorityId);
  }

  public function scopeByType($query, $typeId)
  {
    return $query->where('ticket_type_id', $typeId);
  }

  public function scopeAssignedTo($query, $userId)
  {
    return $query->where('assigned_to', $userId);
  }

  public function scopeForUser($query, $userId)
  {
    return $query->where('user_id', $userId);
  }

  public function scopeOpen($query)
  {
    return $query->whereNotIn('ticket_status_id', [3, 4]); // Not closed/resolved
  }

  public function scopeClosed($query)
  {
    return $query->whereIn('ticket_status_id', [3, 4]); // Closed/resolved
  }

  public function scopeUrgent($query)
  {
    return $query->where('ticket_priority_id', 1); // Assuming 1 = Urgent
  }

  public function scopeHigh($query)
  {
    return $query->where('ticket_priority_id', 2); // Assuming 2 = High
  }

  public function scopeCreatedBetween($query, $startDate, $endDate)
  {
    return $query->whereBetween('created_at', [$startDate, $endDate]);
  }

  public function scopeResolvedBetween($query, $startDate, $endDate)
  {
    return $query->whereBetween('resolved_at', [$startDate, $endDate])
                 ->whereNotNull('resolved_at');
  }

  public function scopeWithAllRelations($query)
  {
    return $query->with([
      'user', 'assignedTo', 'location', 'asset', 
      'ticket_status', 'ticket_priority', 'ticket_type'
    ]);
  }

  public function scopeWithRelations($query)
  {
    return $query->with([
      'user', 'assignedTo', 'ticket_status', 'ticket_priority', 'asset'
    ]);
  }

  // Accessors
  public function getIsOverdueAttribute()
  {
    return $this->sla_due && $this->sla_due->isPast() && 
           !in_array($this->ticket_status_id, [3, 4]);
  }

  public function getTimeRemainingAttribute()
  {
    if (!$this->sla_due) return null;
    
    $diff = now()->diffInHours($this->sla_due, false);
    return $diff > 0 ? $diff : 0;
  }

  public function getSlaStatusAttribute()
  {
    if (!$this->sla_due) return 'No SLA';
    
    if ($this->isOverdue) return 'Overdue';
    
    $hoursRemaining = $this->time_remaining;
    if ($hoursRemaining <= 2) return 'Critical';
    if ($hoursRemaining <= 8) return 'Warning';
    
    return 'On Track';
  }

  public function getSlaStatusColorAttribute()
  {
    $colors = [
      'Overdue' => 'red',
      'Critical' => 'orange', 
      'Warning' => 'yellow',
      'On Track' => 'green',
      'No SLA' => 'gray'
    ];
    
    return $colors[$this->sla_status] ?? 'gray';
  }

  // Methods
  public function assignTo($userId, $type = 'manual')
  {
    $this->update([
      'assigned_to' => $userId,
      'assigned_at' => now(),
      'assignment_type' => $type
    ]);
    
    // Auto-create daily activity untuk assigned user
    if ($userId) {
      DailyActivity::create([
        'user_id' => $userId,
        'activity_date' => today(),
        'description' => "Menerima assignment ticket: {$this->ticket_code} - {$this->subject}",
        'ticket_id' => $this->id,
        'type' => 'auto_from_ticket'
      ]);
    }
  }

  public function markFirstResponse()
  {
    if (!$this->first_response_at) {
      $this->update(['first_response_at' => now()]);
    }
  }

  public function markResolved()
  {
    $this->update([
      'resolved_at' => now(),
      'ticket_status_id' => 3 // Assuming 3 = Resolved
    ]);
    
    // Auto-create daily activity
    DailyActivity::createFromTicketCompletion($this);
  }

  public function getMaintenanceHistoryAttribute()
  {
    if (!$this->asset_id) return collect();
    
    return self::where('asset_id', $this->asset_id)
               ->where('id', '!=', $this->id)
               ->with(['ticket_status', 'ticket_priority', 'assignedTo'])
               ->orderBy('created_at', 'desc')
               ->get();
  }
}
