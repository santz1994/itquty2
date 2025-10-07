<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DailyActivity extends Model
{
    protected $fillable = [
        'user_id', 'activity_date', 'description', 'ticket_id', 'type', 'activity_type'
    ];

    protected $dates = ['activity_date'];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    // Scopes
    public function scopeForDate($query, $date)
    {
        return $query->whereDate('activity_date', $date);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForMonth($query, $year, $month)
    {
        return $query->whereYear('activity_date', $year)
                     ->whereMonth('activity_date', $month);
    }

    // Static methods
    public static function createFromTicketCompletion(Ticket $ticket)
    {
        return self::create([
            'user_id' => $ticket->assigned_to,
            'activity_date' => now()->toDateString(),
            'description' => "Menyelesaikan tiket #{$ticket->ticket_code}: {$ticket->subject}",
            'ticket_id' => $ticket->id,
            'type' => 'auto_from_ticket'
        ]);
    }
}