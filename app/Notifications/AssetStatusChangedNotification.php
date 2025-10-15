<?php

namespace App\Notifications;

use App\Asset;
use App\Status;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AssetStatusChangedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $asset;
    protected $oldStatus;
    protected $newStatus;
    protected $changedBy;
    protected $notes;

    /**
     * Create a new notification instance.
     */
    public function __construct(Asset $asset, ?Status $oldStatus, Status $newStatus, User $changedBy, ?string $notes = null)
    {
        $this->asset = $asset;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
        $this->changedBy = $changedBy;
        $this->notes = $notes;
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
        $mailMessage = (new MailMessage)
            ->subject('Asset Status Changed: ' . $this->asset->name)
            ->line('The status of an asset has been changed.')
            ->line('**Asset:** ' . $this->asset->name)
            ->line('**Asset Tag:** ' . $this->asset->asset_tag)
            ->line('**Old Status:** ' . ($this->oldStatus ? $this->oldStatus->name : 'None'))
            ->line('**New Status:** ' . $this->newStatus->name)
            ->line('**Changed By:** ' . $this->changedBy->name);

        if ($this->notes) {
            $mailMessage->line('**Notes:** ' . $this->notes);
        }

        $mailMessage->action('View Asset', url('/assets/' . $this->asset->id))
            ->line('This notification was sent to keep you informed of asset status changes.');

        return $mailMessage;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'asset_id' => $this->asset->id,
            'asset_name' => $this->asset->name,
            'asset_tag' => $this->asset->asset_tag,
            'old_status_id' => $this->oldStatus?->id,
            'old_status_name' => $this->oldStatus?->name,
            'new_status_id' => $this->newStatus->id,
            'new_status_name' => $this->newStatus->name,
            'changed_by_id' => $this->changedBy->id,
            'changed_by_name' => $this->changedBy->name,
            'notes' => $this->notes,
            'url' => url('/assets/' . $this->asset->id),
            'message' => 'Asset ' . $this->asset->name . ' status changed from ' . 
                         ($this->oldStatus?->name ?? 'None') . ' to ' . $this->newStatus->name,
            'type' => 'asset_status_changed',
            'icon' => 'fa-exchange',
            'color' => 'info'
        ];
    }
}
