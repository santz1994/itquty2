<?php

namespace App\Notifications;

use App\Ticket;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketAssignedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $ticket;
    protected $assigner;

    /**
     * Create a new notification instance.
     */
    public function __construct(Ticket $ticket, User $assigner)
    {
        $this->ticket = $ticket;
        $this->assigner = $assigner;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('New Ticket Assigned: #' . $this->ticket->id)
                    ->line('A new ticket has been assigned to you.')
                    ->line('**Ticket ID:** #' . $this->ticket->id)
                    ->line('**Subject:** ' . $this->ticket->subject)
                    ->line('**Priority:** ' . optional($this->ticket->priority)->name)
                    ->line('**Assigned by:** ' . $this->assigner->name)
                    ->action('View Ticket', url('/tickets/' . $this->ticket->id))
                    ->line('Please review and respond to this ticket as soon as possible.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'ticket_id' => $this->ticket->id,
            'ticket_subject' => $this->ticket->subject,
            'ticket_priority' => optional($this->ticket->priority)->name,
            'assigner_id' => $this->assigner->id,
            'assigner_name' => $this->assigner->name,
            'url' => url('/tickets/' . $this->ticket->id),
            'message' => 'Ticket #' . $this->ticket->id . ' has been assigned to you by ' . $this->assigner->name,
            'type' => 'ticket_assigned',
            'icon' => 'fa-ticket',
            'color' => 'info'
        ];
    }
}
