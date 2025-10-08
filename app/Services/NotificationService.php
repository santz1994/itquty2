<?php

namespace App\Services;

use App\Notification;
use App\Ticket;
use App\Asset;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class NotificationService
{
    /**
     * Check for overdue tickets and create notifications
     */
    public function checkOverdueTickets(): int
    {
        $overdueTickets = Ticket::where('sla_due', '<', now())
                               ->whereNull('resolved_at')
                               ->get();

        $count = 0;
        foreach ($overdueTickets as $ticket) {
            try {
                Notification::createTicketOverdue($ticket);
                $count++;
            } catch (\Exception $e) {
                Log::error('Failed to create overdue ticket notification', [
                    'ticket_id' => $ticket->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return $count;
    }

    /**
     * Check for expiring warranties and create notifications
     */
    public function checkExpiringWarranties(int $days = 30): int
    {
        $expiringAssets = Asset::whereNotNull('purchase_date')
                              ->whereNotNull('warranty_months')
                              ->whereRaw('DATE_ADD(purchase_date, INTERVAL warranty_months MONTH) BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL ? DAY)', [$days])
                              ->with(['assignedTo', 'model'])
                              ->get();

        $count = 0;
        foreach ($expiringAssets as $asset) {
            try {
                $notification = Notification::createWarrantyExpiring($asset);
                if ($notification) {
                    $count++;
                }
            } catch (\Exception $e) {
                Log::error('Failed to create warranty expiring notification', [
                    'asset_id' => $asset->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return $count;
    }

    /**
     * Send notifications for ticket assignments
     */
    public function notifyTicketAssignment(Ticket $ticket, User $user): bool
    {
        try {
            Notification::createTicketAssigned($ticket, $user);
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to create ticket assignment notification', [
                'ticket_id' => $ticket->id,
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send notifications for asset assignments
     */
    public function notifyAssetAssignment(Asset $asset, User $user): bool
    {
        try {
            Notification::createAssetAssigned($asset, $user);
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to create asset assignment notification', [
                'asset_id' => $asset->id,
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Create system maintenance notification
     */
    public function notifySystemMaintenance(string $message, Carbon $scheduledTime, array $userIds = null): int
    {
        $title = 'Scheduled System Maintenance';
        $fullMessage = "System maintenance is scheduled for " . $scheduledTime->format('d M Y H:i') . ". " . $message;
        
        try {
            $notifications = Notification::createSystemAlert($title, $fullMessage, $userIds, 'high');
            return count($notifications);
        } catch (\Exception $e) {
            Log::error('Failed to create system maintenance notifications', [
                'error' => $e->getMessage()
            ]);
            return 0;
        }
    }

    /**
     * Clean up old notifications (older than specified days)
     */
    public function cleanupOldNotifications(int $days = 90): int
    {
        return Notification::where('created_at', '<', now()->subDays($days))
                          ->delete();
    }

    /**
     * Get notification statistics
     */
    public function getStatistics(): array
    {
        return [
            'total' => Notification::count(),
            'unread' => Notification::unread()->count(),
            'today' => Notification::whereDate('created_at', today())->count(),
            'this_week' => Notification::where('created_at', '>=', now()->startOfWeek())->count(),
            'by_type' => Notification::selectRaw('type, COUNT(*) as count')
                                   ->groupBy('type')
                                   ->pluck('count', 'type')
                                   ->toArray(),
            'by_priority' => Notification::selectRaw('priority, COUNT(*) as count')
                                       ->groupBy('priority')
                                       ->pluck('count', 'priority')
                                       ->toArray(),
        ];
    }

    /**
     * Get user notification summary
     */
    public function getUserSummary(int $userId): array
    {
        return [
            'total' => Notification::forUser($userId)->count(),
            'unread' => Notification::forUser($userId)->unread()->count(),
            'recent' => Notification::getRecentForUser($userId, 5),
            'by_type' => Notification::forUser($userId)
                                   ->selectRaw('type, COUNT(*) as count')
                                   ->groupBy('type')
                                   ->pluck('count', 'type')
                                   ->toArray(),
        ];
    }

    /**
     * Send daily digest to admins
     */
    public function sendDailyDigest(): int
    {
        $adminUsers = User::role(['Admin', 'Super Admin'])->get();
        $count = 0;

        foreach ($adminUsers as $admin) {
            $summary = $this->getUserSummary($admin->id);
            
            if ($summary['unread'] > 0) {
                $message = "You have {$summary['unread']} unread notifications. ";
                
                // Add breakdown by type
                $typeBreakdown = [];
                foreach ($summary['by_type'] as $type => $typeCount) {
                    $typeBreakdown[] = ucfirst(str_replace('_', ' ', $type)) . ": {$typeCount}";
                }
                
                if (!empty($typeBreakdown)) {
                    $message .= "Breakdown: " . implode(', ', $typeBreakdown);
                }

                try {
                    Notification::create([
                        'user_id' => $admin->id,
                        'type' => 'daily_digest',
                        'title' => 'Daily Notification Digest',
                        'message' => $message,
                        'priority' => 'normal',
                        'action_url' => url('/notifications'),
                    ]);
                    $count++;
                } catch (\Exception $e) {
                    Log::error('Failed to create daily digest notification', [
                        'user_id' => $admin->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }

        return $count;
    }

    /**
     * Run all automatic notification checks
     */
    public function runAutomaticChecks(): array
    {
        $results = [
            'overdue_tickets' => $this->checkOverdueTickets(),
            'expiring_warranties' => $this->checkExpiringWarranties(),
            'cleanup_count' => $this->cleanupOldNotifications(),
        ];

        Log::info('Automatic notification checks completed', $results);
        
        return $results;
    }
}