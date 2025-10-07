<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Auth;

class Asset extends Model
{
  /**
   * Mass assignable attributes
   * @var array
   */
  protected $fillable = [
    'asset_tag', 'serial_number', 'model_id', 'division_id', 'supplier_id', 
    'purchase_date', 'warranty_months', 'warranty_type_id', 'invoice_id', 
    'ip_address', 'mac_address', 'qr_code', 'status_id', 'assigned_to', 'notes'
  ];

  protected $dates = ['purchase_date'];

  protected static function boot()
  {
    parent::boot();
    
    static::creating(function ($asset) {
      if (!$asset->qr_code) {
        $asset->qr_code = self::generateQRCode();
      }
    });
  }

  public static function generateQRCode()
  {
    return 'AST-' . strtoupper(uniqid());
  }

  /**
   * Generate QR Code URL for mobile access
   */
  public function getQRCodeUrlAttribute()
  {
    return route('assets.qr', $this->qr_code);
  }

  /**
   * Generate QR Code image
   */
  public function generateQRCodeImage()
  {
    return QrCode::size(200)->generate($this->qr_code_url);
  }

  // Relationships
  public function model()
  {
    return $this->belongsTo(AssetModel::class, 'model_id');
  }

  public function division()
  {
    return $this->belongsTo(Division::class);
  }

  public function supplier()
  {
    return $this->belongsTo(Supplier::class);
  }

  public function movement()
  {
    return $this->belongsTo(Movement::class);
  }

  public function warranty_type()
  {
    return $this->belongsTo(WarrantyType::class);
  }

  public function invoice()
  {
    return $this->belongsTo(Invoice::class);
  }

  public function status()
  {
    return $this->belongsTo(Status::class);
  }

  public function assignedTo()
  {
    return $this->belongsTo(User::class, 'assigned_to');
  }

  public function tickets()
  {
    return $this->hasMany(Ticket::class);
  }

  public function assetRequests()
  {
    return $this->hasMany(AssetRequest::class, 'fulfilled_asset_id');
  }

  // Scopes
  public function scopeInUse($query)
  {
    return $query->where('status_id', 1); // Assuming 1 = In Use
  }

  public function scopeInStock($query)
  {
    return $query->where('status_id', 2); // Assuming 2 = In Stock
  }

  public function scopeInRepair($query)
  {
    return $query->where('status_id', 3); // Assuming 3 = In Repair
  }

  public function scopeDisposed($query)
  {
    return $query->where('status_id', 4); // Assuming 4 = Disposed
  }

  // Accessors
  public function getTicketHistoryAttribute()
  {
    return $this->tickets()
                ->with(['user', 'ticket_status', 'ticket_priority'])
                ->orderBy('created_at', 'desc')
                ->get();
  }

  public function getIsLemonAssetAttribute()
  {
    // Asset yang sering rusak (>3 tiket dalam 6 bulan terakhir)
    $recentTickets = $this->tickets()
                          ->where('created_at', '>=', now()->subMonths(6))
                          ->count();
    
    return $recentTickets > 3;
  }

  public function getWarrantyStatusAttribute()
  {
    if (!$this->purchase_date || !$this->warranty_months) {
      return 'No warranty info';
    }

    $warrantyExpiry = $this->purchase_date->addMonths($this->warranty_months);
    
    if ($warrantyExpiry->isPast()) {
      return 'Expired';
    } elseif ($warrantyExpiry->diffInDays(now()) <= 30) {
      return 'Expiring soon';
    } else {
      return 'Active';
    }
  }

  /**
   * Get ticket history for this asset
   */
  public function getTicketHistory()
  {
    return $this->tickets()
                ->with(['user', 'ticket_status', 'ticket_priority', 'ticket_type'])
                ->orderBy('created_at', 'desc')
                ->get();
  }

  /**
   * Get maintenance tickets count
   */
  public function getMaintenanceTicketsCount()
  {
    return $this->tickets()
                ->where('ticket_type_id', 1) // Assuming 1 = Maintenance
                ->count();
  }



  /**
   * Get average resolution time for this asset's tickets
   */
  public function getAverageResolutionTime()
  {
    $resolvedTickets = $this->tickets()
                            ->whereNotNull('resolved_at')
                            ->get();

    if ($resolvedTickets->isEmpty()) {
      return null;
    }

    $totalMinutes = 0;
    foreach ($resolvedTickets as $ticket) {
      $totalMinutes += $ticket->created_at->diffInMinutes($ticket->resolved_at);
    }

    return round($totalMinutes / $resolvedTickets->count());
  }

  /**
   * Get recent issues (last 30 days)
   */
  public function getRecentIssues()
  {
    return $this->tickets()
                ->where('created_at', '>=', Carbon::now()->subDays(30))
                ->with(['ticket_status', 'ticket_priority'])
                ->orderBy('created_at', 'desc')
                ->get();
  }

  /**
   * Log maintenance activity
   */
  public function logMaintenanceActivity($description, $userId = null)
  {
    DailyActivity::create([
      'user_id' => $userId ?? Auth::id(),
      'activity_date' => Carbon::today(),
      'type' => 'maintenance',
      'description' => "Asset {$this->asset_tag}: {$description}",
      'duration_minutes' => null,
      'notes' => "Automated log entry for asset maintenance"
    ]);
  }
}
