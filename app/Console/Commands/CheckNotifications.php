<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NotificationService;

class CheckNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:check 
                           {--overdue : Check for overdue tickets}
                           {--warranty : Check for expiring warranties}
                           {--cleanup : Clean up old notifications}
                           {--digest : Send daily digest to admins}
                           {--all : Run all checks}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for and create automatic notifications';

    /**
     * The notification service instance.
     *
     * @var NotificationService
     */
    protected $notificationService;

    /**
     * Create a new command instance.
     *
     * @param NotificationService $notificationService
     */
    public function __construct(NotificationService $notificationService)
    {
        parent::__construct();
        $this->notificationService = $notificationService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting notification checks...');

        $results = [];

        if ($this->option('all') || $this->option('overdue')) {
            $this->info('Checking for overdue tickets...');
            $count = $this->notificationService->checkOverdueTickets();
            $results['overdue_tickets'] = $count;
            $this->info("Created {$count} overdue ticket notifications");
        }

        if ($this->option('all') || $this->option('warranty')) {
            $this->info('Checking for expiring warranties...');
            $count = $this->notificationService->checkExpiringWarranties(30);
            $results['expiring_warranties'] = $count;
            $this->info("Created {$count} warranty expiring notifications");
        }

        if ($this->option('all') || $this->option('cleanup')) {
            $this->info('Cleaning up old notifications...');
            $count = $this->notificationService->cleanupOldNotifications(90);
            $results['cleaned_up'] = $count;
            $this->info("Cleaned up {$count} old notifications");
        }

        if ($this->option('all') || $this->option('digest')) {
            $this->info('Sending daily digest to admins...');
            $count = $this->notificationService->sendDailyDigest();
            $results['daily_digest'] = $count;
            $this->info("Sent daily digest to {$count} admins");
        }

        if (!$this->option('overdue') && !$this->option('warranty') && 
            !$this->option('cleanup') && !$this->option('digest') && !$this->option('all')) {
            $this->info('Running all automatic checks...');
            $results = $this->notificationService->runAutomaticChecks();
            $this->info("Overdue tickets: {$results['overdue_tickets']}");
            $this->info("Expiring warranties: {$results['expiring_warranties']}");
            $this->info("Cleaned up: {$results['cleanup_count']}");
        }

        // Display statistics
        $stats = $this->notificationService->getStatistics();
        $this->newLine();
        $this->info('Current notification statistics:');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total notifications', $stats['total']],
                ['Unread notifications', $stats['unread']],
                ['Created today', $stats['today']],
                ['Created this week', $stats['this_week']],
            ]
        );

        if (!empty($stats['by_type'])) {
            $this->newLine();
            $this->info('Notifications by type:');
            $typeData = [];
            foreach ($stats['by_type'] as $type => $count) {
                $typeData[] = [ucfirst(str_replace('_', ' ', $type)), $count];
            }
            $this->table(['Type', 'Count'], $typeData);
        }

        $this->info('Notification checks completed!');
        return 0;
    }
}
