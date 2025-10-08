<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

class Notification extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id', 'type', 'title', 'message', 'data', 'priority', 
        'is_read', 'read_at', 'action_url'
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    // ========================
    // RELATIONSHIPS
    // ========================
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ========================
    // SCOPES
    // ========================
    
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // ========================
    // ACCESSORS & MUTATORS
    // ========================
    
    /**
     * Get priority badge HTML
     */
    protected function priorityBadge(): Attribute
    {
        return Attribute::make(
            get: function () {
                $badges = [
                    'low' => '<span class="badge badge-secondary">Low</span>',
                    'normal' => '<span class="badge badge-info">Normal</span>',
                    'high' => '<span class="badge badge-warning">High</span>',
                    'urgent' => '<span class="badge badge-danger">Urgent</span>',
                ];
                return $badges[$this->priority] ?? '<span class="badge badge-light">Unknown</span>';
            }
        );
    }

    /**
     * Get type badge HTML
     */
    protected function typeBadge(): Attribute
    {
        return Attribute::make(
            get: function () {
                $badges = [
                    'ticket_overdue' => '<span class="badge badge-danger"><i class="fas fa-clock"></i> Overdue</span>',
                    'warranty_expiring' => '<span class="badge badge-warning"><i class="fas fa-shield-alt"></i> Warranty</span>',
                    'asset_assigned' => '<span class="badge badge-success"><i class="fas fa-laptop"></i> Asset</span>',
                    'ticket_assigned' => '<span class="badge badge-info"><i class="fas fa-ticket-alt"></i> Ticket</span>',
                    'system_alert' => '<span class="badge badge-dark"><i class="fas fa-exclamation-triangle"></i> System</span>',
                ];
                return $badges[$this->type] ?? '<span class="badge badge-light">' . ucfirst(str_replace('_', ' ', $this->type)) . '</span>';
            }
        );
    }

    /**
     * Get formatted creation time
     */
    protected function timeAgo(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->created_at->diffForHumans()
        );
    }

    /**
     * Get icon class based on type
     */
    protected function iconClass(): Attribute
    {
        return Attribute::make(
            get: function () {
                $icons = [
                    'ticket_overdue' => 'fas fa-clock text-danger',
                    'warranty_expiring' => 'fas fa-shield-alt text-warning',
                    'asset_assigned' => 'fas fa-laptop text-success',
                    'ticket_assigned' => 'fas fa-ticket-alt text-info',
                    'system_alert' => 'fas fa-exclamation-triangle text-dark',
                ];
                return $icons[$this->type] ?? 'fas fa-bell text-muted';
            }
        );
    }

    // ========================
    // HELPER METHODS
    // ========================
    
    /**
     * Mark notification as read
     */
    public function markAsRead(): bool
    {
        if ($this->is_read) {
            return false;
        }

        return $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Mark notification as unread
     */
    public function markAsUnread(): bool
    {
        if (!$this->is_read) {
            return false;
        }

        return $this->update([
            'is_read' => false,
            'read_at' => null,
        ]);
    }

    // ========================
    // STATIC FACTORY METHODS
    // ========================
    
    /**
     * Create ticket overdue notification
     */
    public static function createTicketOverdue(Ticket $ticket): self
    {
        return self::create([
            'user_id' => $ticket->assigned_to ?? $ticket->user_id,
            'type' => 'ticket_overdue',
            'title' => 'Ticket Overdue',
            'message' => "Ticket #{$ticket->ticket_code} is overdue. SLA deadline was " . $ticket->sla_due->diffForHumans(),
            'data' => [
                'ticket_id' => $ticket->id,
                'ticket_code' => $ticket->ticket_code,
                'sla_due' => $ticket->sla_due->toISOString(),
            ],
            'priority' => 'urgent',
            'action_url' => url('/tickets/' . $ticket->id),
        ]);
    }

    /**
     * Create warranty expiring notification
     */
    public static function createWarrantyExpiring(Asset $asset): ?self
    {
        $expiryDate = $asset->warranty_expiry_date;
        $assignedUser = $asset->assignedTo;
        
        // If asset has assigned user, notify them, otherwise notify first admin
        $userId = $assignedUser ? $assignedUser->id : User::role(['admin', 'super-admin'])->first()?->id;
        
        if (!$userId) return null;
        
        return self::create([
            'user_id' => $userId,
            'type' => 'warranty_expiring',
            'title' => 'Warranty Expiring Soon',
            'message' => "Asset {$asset->asset_tag} warranty expires on " . $expiryDate->format('d M Y'),
            'data' => [
                'asset_id' => $asset->id,
                'asset_tag' => $asset->asset_tag,
                'expiry_date' => $expiryDate->toISOString(),
            ],
            'priority' => 'high',
            'action_url' => url('/assets/' . $asset->id),
        ]);
    }

    /**
     * Create asset assigned notification
     */
    public static function createAssetAssigned(Asset $asset, User $user): self
    {
        return self::create([
            'user_id' => $user->id,
            'type' => 'asset_assigned',
            'title' => 'Asset Assigned',
            'message' => "Asset {$asset->asset_tag} has been assigned to you",
            'data' => [
                'asset_id' => $asset->id,
                'asset_tag' => $asset->asset_tag,
                'model' => $asset->model->name ?? 'Unknown Model',
            ],
            'priority' => 'normal',
            'action_url' => url('/assets/' . $asset->id),
        ]);
    }

    /**
     * Create ticket assigned notification
     */
    public static function createTicketAssigned(Ticket $ticket, User $user): self
    {
        return self::create([
            'user_id' => $user->id,
            'type' => 'ticket_assigned',
            'title' => 'Ticket Assigned',
            'message' => "Ticket #{$ticket->ticket_code} has been assigned to you",
            'data' => [
                'ticket_id' => $ticket->id,
                'ticket_code' => $ticket->ticket_code,
                'subject' => $ticket->subject,
                'priority' => $ticket->ticket_priority->name ?? 'Normal',
            ],
            'priority' => strtolower($ticket->ticket_priority->name ?? 'normal'),
            'action_url' => url('/tickets/' . $ticket->id),
        ]);
    }

    /**
     * Create system alert notification
     */
    public static function createSystemAlert(string $title, string $message, array $userIds = null, string $priority = 'normal'): array
    {
        if (!$userIds) {
            $userIds = User::role(['admin', 'super-admin'])->pluck('id');
        }

        $notifications = [];
        foreach ($userIds as $userId) {
            $notifications[] = self::create([
                'user_id' => $userId,
                'type' => 'system_alert',
                'title' => $title,
                'message' => $message,
                'priority' => $priority,
            ]);
        }

        return $notifications;
    }

    // ========================
    // BULK OPERATIONS
    // ========================
    
    /**
     * Mark all notifications as read for user
     */
    public static function markAllAsReadForUser(int $userId): int
    {
        return self::where('user_id', $userId)
                  ->where('is_read', false)
                  ->update([
                      'is_read' => true,
                      'read_at' => now(),
                  ]);
    }

    /**
     * Get unread count for user
     */
    public static function getUnreadCountForUser(int $userId): int
    {
        return self::where('user_id', $userId)
                  ->where('is_read', false)
                  ->count();
    }

    /**
     * Get recent notifications for user
     */
    public static function getRecentForUser(int $userId, int $limit = 10)
    {
        return self::where('user_id', $userId)
                  ->orderBy('created_at', 'desc')
                  ->limit($limit)
                  ->get();
    }
}
