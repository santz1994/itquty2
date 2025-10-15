<?php

use Illuminate\Database\Seeder;
use App\SlaPolicy;
use App\TicketsPriority;
use Illuminate\Support\Facades\DB;

class SlaPoliciesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get ticket priorities
        $urgent = TicketsPriority::where('name', 'Urgent')->first();
        $high = TicketsPriority::where('name', 'High')->first();
        $normal = TicketsPriority::where('name', 'Normal')->first();
        $low = TicketsPriority::where('name', 'Low')->first();
        
        $policies = [
            [
                'name' => 'Urgent Priority SLA',
                'description' => 'SLA policy for urgent priority tickets requiring immediate attention',
                'response_time' => 60,        // 1 hour first response
                'resolution_time' => 240,     // 4 hours resolution
                'priority_id' => $urgent?->id ?? 1,
                'business_hours_only' => false, // 24/7
                'escalation_time' => 120,     // Escalate after 2 hours
                'escalate_to_user_id' => null,
                'is_active' => true,
            ],
            [
                'name' => 'High Priority SLA',
                'description' => 'SLA policy for high priority tickets',
                'response_time' => 240,       // 4 hours first response
                'resolution_time' => 1440,    // 24 hours (1 day) resolution
                'priority_id' => $high?->id ?? 2,
                'business_hours_only' => true, // Business hours only
                'escalation_time' => 720,     // Escalate after 12 hours
                'escalate_to_user_id' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Normal Priority SLA',
                'description' => 'SLA policy for normal/medium priority tickets',
                'response_time' => 1440,      // 24 hours (1 day) first response
                'resolution_time' => 4320,    // 72 hours (3 days) resolution
                'priority_id' => $normal?->id ?? 3,
                'business_hours_only' => true, // Business hours only
                'escalation_time' => null,
                'escalate_to_user_id' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Low Priority SLA',
                'description' => 'SLA policy for low priority tickets',
                'response_time' => 2880,      // 48 hours (2 days) first response
                'resolution_time' => 10080,   // 168 hours (1 week) resolution
                'priority_id' => $low?->id ?? 4,
                'business_hours_only' => true, // Business hours only
                'escalation_time' => null,
                'escalate_to_user_id' => null,
                'is_active' => true,
            ],
        ];
        
        foreach ($policies as $policy) {
            // Check if policy already exists
            $existingPolicy = SlaPolicy::where('priority_id', $policy['priority_id'])->first();
            
            if (!$existingPolicy) {
                SlaPolicy::create($policy);
                $this->command->info("Created SLA policy: {$policy['name']}");
            } else {
                $this->command->warn("SLA policy already exists for priority ID: {$policy['priority_id']}");
            }
        }
        
        $this->command->info('SLA Policies seeded successfully!');
    }
}
