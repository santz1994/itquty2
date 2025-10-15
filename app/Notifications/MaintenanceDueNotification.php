<?php

namespace App\Notifications;

use App\Asset;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

class MaintenanceDueNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $asset;
    protected $dueDate;
    protected $daysUntilDue;

    /**
     * Create a new notification instance.
     */
    public function __construct(Asset $asset, Carbon $dueDate)
    {
        $this->asset = $asset;
        $this->dueDate = $dueDate;
        $this->daysUntilDue = now()->diffInDays($dueDate, false);
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
        $urgency = $this->daysUntilDue <= 0 ? 'OVERDUE' : 'Due in ' . $this->daysUntilDue . ' days';
        
        $mailMessage = (new MailMessage)
            ->subject('Maintenance ' . $urgency . ': ' . $this->asset->name)
            ->line('Asset maintenance is ' . strtolower($urgency) . '.')
            ->line('**Asset:** ' . $this->asset->name)
            ->line('**Asset Tag:** ' . $this->asset->asset_tag)
            ->line('**Location:** ' . optional($this->asset->location)->name)
            ->line('**Due Date:** ' . $this->dueDate->format('Y-m-d'));

        if ($this->daysUntilDue <= 0) {
            $mailMessage->line('⚠️ **This maintenance is overdue!**');
        }

        $mailMessage->action('View Asset', url('/assets/' . $this->asset->id))
            ->line('Please schedule this maintenance as soon as possible.');

        return $mailMessage;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $isOverdue = $this->daysUntilDue <= 0;
        
        return [
            'asset_id' => $this->asset->id,
            'asset_name' => $this->asset->name,
            'asset_tag' => $this->asset->asset_tag,
            'location' => optional($this->asset->location)->name,
            'due_date' => $this->dueDate->format('Y-m-d'),
            'days_until_due' => $this->daysUntilDue,
            'is_overdue' => $isOverdue,
            'url' => url('/assets/' . $this->asset->id),
            'message' => $isOverdue 
                ? 'Maintenance for ' . $this->asset->name . ' is OVERDUE!' 
                : 'Maintenance for ' . $this->asset->name . ' is due in ' . $this->daysUntilDue . ' days',
            'type' => 'maintenance_due',
            'icon' => $isOverdue ? 'fa-exclamation-triangle' : 'fa-wrench',
            'color' => $isOverdue ? 'danger' : 'warning'
        ];
    }
}
