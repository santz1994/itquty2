<?php

namespace App\Notifications;

use App\Export;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * ExportCompleted Notification
 * 
 * Sends email notification when an async export completes.
 */
class ExportCompleted extends Notification
{
    use Queueable;

    public Export $export;
    public bool $isError;

    /**
     * Create a new notification instance
     */
    public function __construct(Export $export)
    {
        $this->export = $export;
        $this->isError = $export->status === 'failed';
    }

    /**
     * Get the notification's delivery channels
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification
     */
    public function toMail(object $notifiable): MailMessage
    {
        if ($this->isError) {
            return $this->buildErrorEmail($notifiable);
        }

        return $this->buildSuccessEmail($notifiable);
    }

    /**
     * Build success email
     */
    protected function buildSuccessEmail($notifiable): MailMessage
    {
        $downloadUrl = url("/api/v1/exports/{$this->export->export_id}/download");

        return (new MailMessage)
            ->subject("Your {$this->export->export_format} Export is Ready")
            ->greeting("Hello {$notifiable->name},")
            ->line("Your {$this->export->resource_type} export has completed successfully!")
            ->line("**Export Details:**")
            ->line("- Format: " . strtoupper($this->export->export_format))
            ->line("- Total Items: " . number_format($this->export->total_items))
            ->line("- Exported Items: " . number_format($this->export->exported_items))
            ->line("- File Size: " . $this->formatBytes($this->export->file_size))
            ->line("- Created: " . $this->export->created_at->format('Y-m-d H:i:s'))
            ->line("- Duration: " . $this->export->getDurationSeconds() . " seconds")
            ->line("**Download Link:**")
            ->action('Download Export', $downloadUrl)
            ->line("**Note:** This download link expires on " . $this->export->expires_at->format('Y-m-d H:i:s') . ".")
            ->line("You can also view your export history in your account.")
            ->salutation('Best regards, ITQuty System');
    }

    /**
     * Build error email
     */
    protected function buildErrorEmail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->error()
            ->subject("Your Export Failed")
            ->greeting("Hello {$notifiable->name},")
            ->line("Unfortunately, your {$this->export->resource_type} export could not be completed.")
            ->line("**Export Details:**")
            ->line("- Format: " . strtoupper($this->export->export_format))
            ->line("- Resource Type: " . ucfirst($this->export->resource_type))
            ->line("- Status: Failed")
            ->line("**Error Details:**")
            ->line($this->export->error_details['message'] ?? 'Unknown error occurred')
            ->action('Retry Export', url("/api/v1/bulk-operations/{$this->export->export_id}/retry"))
            ->line("Please try again or contact support if the issue persists.")
            ->salutation('Best regards, ITQuty System');
    }

    /**
     * Format bytes to human readable
     */
    protected function formatBytes(?int $bytes, int $precision = 2): string
    {
        if (!$bytes) {
            return 'N/A';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= 1024 ** $pow;

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * Get the array representation of the notification
     */
    public function toArray(object $notifiable): array
    {
        return [
            'export_id' => $this->export->export_id,
            'resource_type' => $this->export->resource_type,
            'format' => $this->export->export_format,
            'status' => $this->export->status,
            'total_items' => $this->export->total_items,
            'exported_items' => $this->export->exported_items,
        ];
    }
}
