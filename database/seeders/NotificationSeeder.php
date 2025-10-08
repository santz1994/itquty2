<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Notification;
use App\User;

class NotificationSeeder extends Seeder
{
    public function run()
    {
        $user = User::first();
        
        if ($user) {
            // Create a few test notifications
            Notification::create([
                'user_id' => $user->id,
                'type' => 'system_alert',
                'title' => 'System Update Complete',
                'message' => 'The IT Asset Management System has been successfully updated with new features including advanced notifications, KPI dashboard, and enhanced reporting capabilities.',
                'priority' => 'normal',
                'action_url' => url('/kpi-dashboard'),
            ]);

            Notification::create([
                'user_id' => $user->id,
                'type' => 'ticket_assigned',
                'title' => 'New Ticket Assigned',
                'message' => 'You have been assigned ticket #TKT-20251008-001 - Network connectivity issue in Division A.',
                'priority' => 'high',
                'data' => [
                    'ticket_id' => 1,
                    'ticket_code' => 'TKT-20251008-001',
                    'subject' => 'Network connectivity issue',
                    'priority' => 'High',
                ],
                'action_url' => url('/tickets/1'),
            ]);

            Notification::create([
                'user_id' => $user->id,
                'type' => 'asset_assigned',
                'title' => 'Asset Assignment',
                'message' => 'Asset LAP-001 (Dell Laptop) has been assigned to you. Please confirm receipt and check the asset condition.',
                'priority' => 'normal',
                'is_read' => true,
                'read_at' => now()->subDays(1),
                'data' => [
                    'asset_id' => 1,
                    'asset_tag' => 'LAP-001',
                    'model' => 'Dell Laptop',
                ],
                'action_url' => url('/assets/1'),
            ]);

            Notification::create([
                'user_id' => $user->id,
                'type' => 'warranty_expiring',
                'title' => 'Warranty Expiring Soon',
                'message' => 'The warranty for asset SRV-001 will expire in 15 days. Please consider renewing or replacing the asset.',
                'priority' => 'high',
                'data' => [
                    'asset_id' => 2,
                    'asset_tag' => 'SRV-001',
                    'expiry_date' => now()->addDays(15)->toISOString(),
                ],
                'action_url' => url('/assets/2'),
            ]);

            echo "Created 4 test notifications for user: {$user->name}\n";
        } else {
            echo "No users found. Please create a user first.\n";
        }
    }
}