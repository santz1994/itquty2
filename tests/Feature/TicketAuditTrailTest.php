<?php

namespace Tests\Feature;

use App\Ticket;
use App\TicketHistory;
use App\TicketsStatus;
use App\TicketsPriority;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature test for ticket audit logging
 * Verifies that all ticket changes are properly recorded in ticket_history
 */
class TicketAuditTrailTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Set up test data
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Create necessary records
        TicketsStatus::factory()->create(['id' => 1, 'name' => 'Open']);
        TicketsStatus::factory()->create(['id' => 2, 'name' => 'In Progress']);
        TicketsStatus::factory()->create(['id' => 3, 'name' => 'Closed']);
        
        TicketsPriority::factory()->create(['id' => 1, 'name' => 'Urgent']);
        TicketsPriority::factory()->create(['id' => 2, 'name' => 'High']);
        TicketsPriority::factory()->create(['id' => 3, 'name' => 'Medium']);
        
        User::factory()->create(['id' => 1, 'name' => 'System User', 'is_active' => 1]);
        User::factory()->create(['id' => 2, 'name' => 'Tech Support', 'is_active' => 1]);
        User::factory()->create(['id' => 3, 'name' => 'Manager', 'is_active' => 1]);
    }

    /**
     * Test that ticket status change is logged to ticket_history
     */
    public function test_ticket_status_change_is_logged()
    {
        // Create a ticket
        $ticket = Ticket::factory()->create([
            'ticket_status_id' => 1, // Open
            'ticket_priority_id' => 1, // Urgent
        ]);
        
        // Verify no history yet
        $this->assertCount(0, $ticket->history);
        
        // Update status
        $this->actingAs(User::find(2))
             ->patch(route('tickets.update', $ticket), [
                 'ticket_status_id' => 2, // In Progress
             ]);
        
        // Refresh ticket to see updated history
        $ticket->refresh();
        
        // Verify history was created
        $history = $ticket->history()->where('field_changed', 'ticket_status_id')->first();
        $this->assertNotNull($history);
        $this->assertEquals('1', $history->old_value);
        $this->assertEquals('2', $history->new_value);
        $this->assertEquals('field_change', $history->change_type);
    }

    /**
     * Test that ticket priority change is logged
     */
    public function test_ticket_priority_change_is_logged()
    {
        $ticket = Ticket::factory()->create([
            'ticket_priority_id' => 1, // Urgent
        ]);
        
        // Update priority
        $ticket->update(['ticket_priority_id' => 2]); // High
        
        // Verify history
        $history = $ticket->history()->where('field_changed', 'ticket_priority_id')->first();
        $this->assertNotNull($history);
        $this->assertEquals('1', $history->old_value);
        $this->assertEquals('2', $history->new_value);
    }

    /**
     * Test that ticket assignment change is logged
     */
    public function test_ticket_assignment_change_is_logged()
    {
        $ticket = Ticket::factory()->create([
            'assigned_to' => null,
        ]);
        
        // Assign ticket
        $ticket->update(['assigned_to' => 2]);
        
        // Verify history
        $history = $ticket->history()->where('field_changed', 'assigned_to')->first();
        $this->assertNotNull($history);
        $this->assertNull($history->old_value); // Was null
        $this->assertEquals('2', $history->new_value); // Now assigned to user 2
    }

    /**
     * Test that multiple changes are independently logged
     */
    public function test_multiple_ticket_changes_are_logged_independently()
    {
        $ticket = Ticket::factory()->create([
            'ticket_status_id' => 1,
            'ticket_priority_id' => 1,
            'assigned_to' => null,
        ]);
        
        // Verify no initial history
        $this->assertCount(0, $ticket->history);
        
        // Change multiple fields
        $ticket->update([
            'ticket_status_id' => 2,
            'ticket_priority_id' => 3,
            'assigned_to' => 2,
        ]);
        
        // Refresh and verify
        $ticket->refresh();
        
        // Should have 3 history records
        $this->assertGreaterThanOrEqual(3, $ticket->history()->count());
        
        // Verify each field was logged
        $statusHistory = $ticket->history()->where('field_changed', 'ticket_status_id')->first();
        $priorityHistory = $ticket->history()->where('field_changed', 'ticket_priority_id')->first();
        $assignmentHistory = $ticket->history()->where('field_changed', 'assigned_to')->first();
        
        $this->assertNotNull($statusHistory);
        $this->assertNotNull($priorityHistory);
        $this->assertNotNull($assignmentHistory);
    }

    /**
     * Test that ticket_history is immutable (cannot update)
     */
    public function test_ticket_history_is_immutable()
    {
        $ticket = Ticket::factory()->create();
        $ticket->update(['ticket_status_id' => 2]);
        
        $history = $ticket->history()->first();
        
        // Attempting to update should throw exception
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('immutable');
        
        $history->update(['old_value' => 'modified']);
    }

    /**
     * Test ticket history filtering scopes
     */
    public function test_ticket_history_filtering_scopes()
    {
        $ticket1 = Ticket::factory()->create();
        $ticket2 = Ticket::factory()->create();
        
        // Create changes
        $ticket1->update(['ticket_status_id' => 2]);
        $ticket2->update(['ticket_priority_id' => 2]);
        
        // Test forField scope
        $statusChanges = TicketHistory::forField('ticket_status_id')->get();
        $this->assertGreaterThan(0, $statusChanges->count());
        $this->assertTrue($statusChanges->every(fn($h) => $h->field_changed === 'ticket_status_id'));
        
        // Test ticket-specific history
        $ticket1->refresh();
        $ticket1History = $ticket1->history;
        $this->assertTrue($ticket1History->every(fn($h) => $h->ticket_id === $ticket1->id));
    }
}
