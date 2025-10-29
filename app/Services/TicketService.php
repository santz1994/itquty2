<?php

namespace App\Services;

use App\Ticket;
use App\DailyActivity;
use App\User;
use App\TicketsStatus;
use App\TicketsEntry;
use App\AdminOnlineStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class TicketService
{
    /**
     * Generate unique ticket code: Prefix-Date-SequentialNumber
     */
    public function generateTicketCode($prefix = 'TIK')
    {
        $date = Carbon::now()->format('Ymd');
        
        // Get today's ticket count for sequential number
        $todayTicketCount = Ticket::whereDate('created_at', Carbon::today())->count();
        $sequentialNumber = str_pad($todayTicketCount + 1, 3, '0', STR_PAD_LEFT);
        
        return "{$prefix}-{$date}-{$sequentialNumber}";
    }

    /**
     * Auto-assign ticket to available admin (simplified version)
     */
    public function autoAssignTicketSimple(Ticket $ticket)
    {
        // Get available admins (users with admin or super-admin role)
        $admins = User::role(['admin', 'super-admin'])->get();
        
        if ($admins->isNotEmpty()) {
            $randomAdmin = $admins->random();
            
            $ticket->update([
                'assigned_to' => $randomAdmin->id,
                'assigned_at' => Carbon::now(),
                'assignment_type' => 'auto'
            ]);
            
            return true;
        }
        
        return false;
    }

    /**
     * Create a new ticket with auto-assignment
     */
    public function createTicket(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Generate ticket code
            $data['ticket_code'] = $this->generateTicketCode();

            // Defensive: ensure ticket_status_id is present and valid
            if (empty($data['ticket_status_id']) || !\App\TicketsStatus::find($data['ticket_status_id'])) {
                // Fallback to default 'Open' status id
                $defaultId = $this->getStatusId('Open');
                Log::warning('ticket_status_id missing or invalid in createTicket payload; defaulting to Open', [
                    'provided_ticket_status_id' => $data['ticket_status_id'] ?? null,
                    'default_ticket_status_id' => $defaultId,
                    'payload_snapshot' => $data
                ]);
                $data['ticket_status_id'] = $defaultId;
            }

            // Create ticket
            $ticket = Ticket::create($data);
            
            // Auto-assign to available admin
            $this->autoAssignTicket($ticket);
            
            // Log maintenance activity if ticket is related to an asset
            // Support multiple assets via pivot 'ticket_assets'. Backfill from single asset_id if present.
            try {
                if (!empty($data['asset_ids']) && is_array($data['asset_ids'])) {
                    $ticket->assets()->sync(array_values($data['asset_ids']));
                } elseif (!empty($data['asset_id'])) {
                    // attach the single asset id (keep for backwards compat)
                    $ticket->assets()->syncWithoutDetaching([$data['asset_id']]);
                }

                // Log maintenance activity for each attached asset (if any)
                foreach ($ticket->assets as $asset) {
                    try {
                        $asset->logMaintenanceActivity("Ticket created: {$ticket->subject}", $ticket->user_id);
                    } catch (\Exception $e) {
                        // ignore per-asset logging errors
                    }
                }
            } catch (\Exception $e) {
                Log::warning('Failed to attach assets to ticket during createTicket: ' . $e->getMessage());
            }
            
            // Send notification (implement later with Reverb)
            // $this->sendTicketCreatedNotification($ticket);
            
            return $ticket;
        });
    }

    /**
     * Auto assign ticket to random online admin
     */
    public function autoAssignTicket(Ticket $ticket)
    {
        $onlineAdmins = AdminOnlineStatus::getOnlineAdmins();
        
        if ($onlineAdmins->isEmpty()) {
            Log::info("No online admins available for ticket {$ticket->ticket_code}");
            return false;
        }

        // Random assignment among online admins
        $selectedAdmin = $onlineAdmins->random();
        
        return $this->assignTicket($ticket, $selectedAdmin->user_id, 'auto');
    }

    /**
     * Manually assign ticket
     */
    public function assignTicket(Ticket $ticket, $adminId, $type = 'manual')
    {
        $admin = User::find($adminId);
        
        if (!$admin || !$admin->hasRole(['admin', 'superadmin'])) {
            throw new \Exception('Invalid admin for assignment');
        }

        $ticket->update([
            'assigned_to' => $adminId,
            'assigned_at' => now(),
            'assignment_type' => $type,
            'ticket_status_id' => $this->getStatusId('In Progress') // Auto update to In Progress
        ]);

        // Add automatic ticket entry for assignment
        $this->addTicketEntry($ticket, $admin->id, "Tiket telah ditugaskan kepada {$admin->name}");

        Log::info("Ticket {$ticket->ticket_code} assigned to admin {$admin->name}");
        
        return true;
    }

    /**
     * Self-assign ticket by admin
     */
    public function selfAssignTicket(Ticket $ticket, $adminId)
    {
        if ($ticket->assigned_to) {
            throw new \Exception('Ticket already assigned');
        }

        return $this->assignTicket($ticket, $adminId, 'manual');
    }

    /**
     * Complete ticket and create daily activity
     */
    public function completeTicket(Ticket $ticket, $resolution = null)
    {
        return DB::transaction(function () use ($ticket, $resolution) {
            $ticket->update([
                'resolved_at' => now(),
                'ticket_status_id' => 3, // Assuming 3 = Resolved
                'closed' => now()
            ]);

            // Auto-create daily activity
            if ($ticket->assigned_to) {
                DailyActivity::createFromTicketCompletion($ticket);
            }

            return $ticket;
        });
    }

    /**
     * Get tickets near SLA deadline
     */
    public function getTicketsNearDeadline($hours = 2)
    {
        return Ticket::nearDeadline($hours)
                    ->with(['user', 'assignedTo', 'ticket_priority'])
                    ->orderBy('sla_due', 'asc')
                    ->get();
    }

    /**
     * Get overdue tickets
     */
    public function getOverdueTickets()
    {
        return Ticket::overdue()
                    ->with(['user', 'assignedTo', 'ticket_priority'])
                    ->orderBy('sla_due', 'asc')
                    ->get();
    }

    /**
     * Get unassigned tickets
     */
    public function getUnassignedTickets()
    {
        return Ticket::unassigned()
                    ->with(['user', 'ticket_priority', 'ticket_type'])
                    ->orderBy('created_at', 'asc')
                    ->get();
    }

    /**
     * Update admin activity status
     */
    public function updateAdminActivity($userId)
    {
        return AdminOnlineStatus::updateActivity($userId);
    }

    /**
     * Get admin performance metrics
     */
    public function getAdminPerformance($adminId, $startDate = null, $endDate = null)
    {
        $startDate = $startDate ?: now()->subMonth();
        $endDate = $endDate ?: now();

        $tickets = Ticket::where('assigned_to', $adminId)
                        ->whereBetween('created_at', [$startDate, $endDate]);

        return [
            'total_assigned' => $tickets->count(),
            'completed' => $tickets->whereNotNull('resolved_at')->count(),
            'overdue' => $tickets->where('sla_due', '<', now())
                              ->whereNull('resolved_at')->count(),
            'avg_resolution_time' => $this->calculateAverageResolutionTime($adminId, $startDate, $endDate),
            'completion_rate' => $this->calculateCompletionRate($adminId, $startDate, $endDate)
        ];
    }

    private function calculateAverageResolutionTime($adminId, $startDate, $endDate)
    {
        $resolvedTickets = Ticket::where('assigned_to', $adminId)
                                ->whereBetween('created_at', [$startDate, $endDate])
                                ->whereNotNull('resolved_at')
                                ->get();

        if ($resolvedTickets->isEmpty()) return 0;

        $totalMinutes = $resolvedTickets->sum(function ($ticket) {
            return $ticket->created_at->diffInMinutes($ticket->resolved_at);
        });

        return round($totalMinutes / $resolvedTickets->count() / 60, 2); // Return in hours
    }

    private function calculateCompletionRate($adminId, $startDate, $endDate)
    {
        $totalTickets = Ticket::where('assigned_to', $adminId)
                             ->whereBetween('created_at', [$startDate, $endDate])
                             ->count();

        if ($totalTickets === 0) return 0;

        $completedTickets = Ticket::where('assigned_to', $adminId)
                                 ->whereBetween('created_at', [$startDate, $endDate])
                                 ->whereNotNull('resolved_at')
                                 ->count();

        return round(($completedTickets / $totalTickets) * 100, 2);
    }

    /**
     * Get status ID by name
     */
    private function getStatusId($statusName)
    {
        $status = TicketsStatus::where('status', $statusName)->first();
        return $status ? $status->id : 1; // Default to 1 if not found
    }

    /**
     * Add ticket entry for logging activities
     */
    public function addTicketEntry(Ticket $ticket, $userId, $message, $isPublic = true)
    {
        return TicketsEntry::create([
            'ticket_id' => $ticket->id,
            'user_id' => $userId,
            'body' => $message,
            'is_public' => $isPublic,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * Add first response to ticket (updates status and logs activity)
     */
    public function addFirstResponse(Ticket $ticket, $userId, $response)
    {
        DB::transaction(function () use ($ticket, $userId, $response) {
            // Mark first response time if not set
            if (!$ticket->first_response_at) {
                $ticket->update([
                    'first_response_at' => now()
                ]);
            }

            // Add the response as ticket entry
            $this->addTicketEntry($ticket, $userId, $response);

            // Update status if still open
            if ($ticket->ticket_status_id == $this->getStatusId('Open')) {
                $ticket->update([
                    'ticket_status_id' => $this->getStatusId('In Progress')
                ]);
            }
        });

        return true;
    }

    /**
     * Update ticket status with automatic logging
     */
    public function updateTicketStatus(Ticket $ticket, $statusName, $userId, $notes = null)
    {
        $oldStatus = $ticket->ticket_status->status ?? 'Unknown';
        $newStatusId = $this->getStatusId($statusName);

        DB::transaction(function () use ($ticket, $newStatusId, $statusName, $oldStatus, $userId, $notes) {
            $ticket->update([
                'ticket_status_id' => $newStatusId
            ]);

            // Log status change
            $message = "Status tiket diubah dari '{$oldStatus}' menjadi '{$statusName}'";
            if ($notes) {
                $message .= "\n\nCatatan: " . $notes;
            }

            $this->addTicketEntry($ticket, $userId, $message);

            // Handle special status changes
            if ($statusName == 'Resolved' && !$ticket->resolved_at) {
                $ticket->update([
                    'resolved_at' => now()
                ]);

                // Create daily activity for resolution
                if ($ticket->assigned_to) {
                    DailyActivity::createFromTicketCompletion($ticket);
                }
            }
        });

        return true;
    }

    /**
     * Close ticket with resolution
     */
    public function closeTicket(Ticket $ticket, $resolution, $userId)
    {
        DB::transaction(function () use ($ticket, $resolution, $userId) {
            $ticket->update([
                'ticket_status_id' => $this->getStatusId('Resolved'),
                'resolved_at' => now(),
                'resolution' => $resolution,
                'closed' => now()
            ]);

            // Log resolution
            $this->addTicketEntry($ticket, $userId, "Tiket diselesaikan.\n\nResolusi:\n" . $resolution);

            // Create daily activity
            if ($ticket->assigned_to) {
                DailyActivity::createFromTicketCompletion($ticket);
            }
        });

        return true;
    }
}