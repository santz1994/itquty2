<?php

namespace App\Observers;

use App\Ticket;
use App\DailyActivity;
use App\Services\CacheService;

class TicketObserver
{
    /**
     * Handle the Ticket "created" event.
     */
    public function created(Ticket $ticket): void
    {
        // Auto create daily activity when ticket is created
        if ($ticket->assigned_to) {
            DailyActivity::create([
                'user_id' => $ticket->assigned_to,
                'activity_date' => now(),
                'description' => 'Tiket baru ditugaskan: ' . $ticket->subject,
                'ticket_id' => $ticket->id,
                'type' => 'ticket_assignment',
                'activity_type' => 'system'
            ]);
        }
    }

    /**
     * Handle the Ticket "updated" event.
     */
    public function updated(Ticket $ticket): void
    {
        // Check if status changed to completed
        if ($ticket->isDirty('ticket_status_id')) {
            $status = $ticket->status;
            if ($status && strtolower($status->name) === 'completed') {
                // Create daily activity for ticket completion
                DailyActivity::create([
                    'user_id' => $ticket->assigned_to ?? $ticket->user_id,
                    'activity_date' => now(),
                    'description' => 'Menyelesaikan Tiket: ' . $ticket->subject . 
                                   ($ticket->asset ? ' - Asset: ' . $ticket->asset->asset_tag : ''),
                    'ticket_id' => $ticket->id,
                    'type' => 'ticket_completion',
                    'activity_type' => 'system'
                ]);

                // Update resolved_at timestamp
                $ticket->resolved_at = now();
                $ticket->saveQuietly(); // Prevent infinite loop
            }
        }

        // Check if ticket is assigned to someone new
        if ($ticket->isDirty('assigned_to') && $ticket->assigned_to) {
            DailyActivity::create([
                'user_id' => $ticket->assigned_to,
                'activity_date' => now(),
                'description' => 'Tiket ditugaskan ulang: ' . $ticket->subject,
                'ticket_id' => $ticket->id,
                'type' => 'ticket_reassignment',
                'activity_type' => 'system'
            ]);
        }
        
        // Clear cache when ticket changes
        app(CacheService::class)->clearCacheOnUpdate('ticket');
    }

    /**
     * Handle the Ticket "deleted" event.
     */
    public function deleted(Ticket $ticket): void
    {
        // Clear cache when ticket is deleted
        app(CacheService::class)->clearCacheOnUpdate('ticket');
    }

    /**
     * Handle the Ticket "restored" event.
     */
    public function restored(Ticket $ticket): void
    {
        //
    }

    /**
     * Handle the Ticket "force deleted" event.
     */
    public function forceDeleted(Ticket $ticket): void
    {
        //
    }
}
