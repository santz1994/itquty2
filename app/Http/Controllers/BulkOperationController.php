<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Ticket;
use App\User;
use App\TicketsStatus;
use App\TicketsPriority;
use App\TicketsType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BulkOperationController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Bulk assign tickets to a user
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bulkAssign(Request $request)
    {
        $request->validate([
            'ticket_ids' => 'required|array|min:1',
            'ticket_ids.*' => 'exists:tickets,id',
            'assigned_to' => 'required|exists:users,id',
        ]);

        try {
            DB::beginTransaction();

            $ticketIds = $request->ticket_ids;
            $assignedTo = $request->assigned_to;
            $user = Auth::user();

            // Check authorization for each ticket
            $tickets = Ticket::whereIn('id', $ticketIds)->get();
            
            foreach ($tickets as $ticket) {
                // Only allow if user can update the ticket
                if (!$user->hasRole('super-admin') && !$user->hasRole('admin') && $ticket->assigned_to != $user->id) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => "You don't have permission to modify ticket #{$ticket->id}"
                    ], 403);
                }
            }

            // Perform bulk assignment
            $updatedCount = Ticket::whereIn('id', $ticketIds)->update([
                'assigned_to' => $assignedTo,
                'updated_at' => now()
            ]);

            // Get assigned user name
            $assignedUser = User::find($assignedTo);

            // Create notifications for assigned user
            foreach ($tickets as $ticket) {
                $ticket->refresh();
                
                // Create notification
                \App\Notification::create([
                    'user_id' => $assignedTo,
                    'title' => 'Ticket Assigned to You',
                    'message' => "Ticket #{$ticket->id} has been assigned to you by " . $user->name,
                    'type' => 'ticket_assigned',
                    'related_id' => $ticket->id,
                    'is_read' => 0
                ]);
            }

            DB::commit();

            Log::info("Bulk assign: User {$user->id} assigned {$updatedCount} tickets to user {$assignedTo}");

            return response()->json([
                'success' => true,
                'message' => "Successfully assigned {$updatedCount} ticket(s) to {$assignedUser->name}",
                'updated_count' => $updatedCount
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bulk assign error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while assigning tickets: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk update ticket status
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'ticket_ids' => 'required|array|min:1',
            'ticket_ids.*' => 'exists:tickets,id',
            'status_id' => 'required|exists:tickets_statuses,id',
        ]);

        try {
            DB::beginTransaction();

            $ticketIds = $request->ticket_ids;
            $statusId = $request->status_id;
            $user = Auth::user();

            // Check authorization
            $tickets = Ticket::whereIn('id', $ticketIds)->get();
            
            foreach ($tickets as $ticket) {
                if (!$user->hasRole('super-admin') && !$user->hasRole('admin') && $ticket->assigned_to != $user->id) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => "You don't have permission to modify ticket #{$ticket->id}"
                    ], 403);
                }
            }

            // Get status name
            $status = TicketsStatus::find($statusId);

            // Check if status is "Resolved" or "Closed" to set resolved_at
            $updateData = [
                'status_id' => $statusId,
                'updated_at' => now()
            ];

            if (in_array(strtolower($status->name), ['resolved', 'closed'])) {
                $updateData['resolved_at'] = now();
            }

            // Perform bulk update
            $updatedCount = Ticket::whereIn('id', $ticketIds)->update($updateData);

            // Create notifications for assigned users
            foreach ($tickets as $ticket) {
                if ($ticket->assigned_to) {
                    \App\Notification::create([
                        'user_id' => $ticket->assigned_to,
                        'title' => 'Ticket Status Updated',
                        'message' => "Ticket #{$ticket->id} status has been changed to {$status->name} by " . $user->name,
                        'type' => 'ticket_status_changed',
                        'related_id' => $ticket->id,
                        'is_read' => 0
                    ]);
                }
            }

            DB::commit();

            Log::info("Bulk status update: User {$user->id} updated {$updatedCount} tickets to status {$statusId}");

            return response()->json([
                'success' => true,
                'message' => "Successfully updated status of {$updatedCount} ticket(s) to {$status->name}",
                'updated_count' => $updatedCount
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bulk status update error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating ticket status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk update ticket priority
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bulkUpdatePriority(Request $request)
    {
        $request->validate([
            'ticket_ids' => 'required|array|min:1',
            'ticket_ids.*' => 'exists:tickets,id',
            'priority_id' => 'required|exists:tickets_priorities,id',
        ]);

        try {
            DB::beginTransaction();

            $ticketIds = $request->ticket_ids;
            $priorityId = $request->priority_id;
            $user = Auth::user();

            // Check authorization
            $tickets = Ticket::whereIn('id', $ticketIds)->get();
            
            foreach ($tickets as $ticket) {
                if (!$user->hasRole('super-admin') && !$user->hasRole('admin') && $ticket->assigned_to != $user->id) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => "You don't have permission to modify ticket #{$ticket->id}"
                    ], 403);
                }
            }

            // Perform bulk update
            $updatedCount = Ticket::whereIn('id', $ticketIds)->update([
                'priority_id' => $priorityId,
                'updated_at' => now()
            ]);

            // Get priority name
            $priority = TicketsPriority::find($priorityId);

            // Create notifications for assigned users
            foreach ($tickets as $ticket) {
                if ($ticket->assigned_to) {
                    \App\Notification::create([
                        'user_id' => $ticket->assigned_to,
                        'title' => 'Ticket Priority Updated',
                        'message' => "Ticket #{$ticket->id} priority has been changed to {$priority->name} by " . $user->name,
                        'type' => 'ticket_priority_changed',
                        'related_id' => $ticket->id,
                        'is_read' => 0
                    ]);
                }
            }

            DB::commit();

            Log::info("Bulk priority update: User {$user->id} updated {$updatedCount} tickets to priority {$priorityId}");

            return response()->json([
                'success' => true,
                'message' => "Successfully updated priority of {$updatedCount} ticket(s) to {$priority->name}",
                'updated_count' => $updatedCount
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bulk priority update error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating ticket priority: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk update ticket category/type
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bulkUpdateCategory(Request $request)
    {
        $request->validate([
            'ticket_ids' => 'required|array|min:1',
            'ticket_ids.*' => 'exists:tickets,id',
            'type_id' => 'required|exists:tickets_types,id',
        ]);

        try {
            DB::beginTransaction();

            $ticketIds = $request->ticket_ids;
            $typeId = $request->type_id;
            $user = Auth::user();

            // Check authorization
            $tickets = Ticket::whereIn('id', $ticketIds)->get();
            
            foreach ($tickets as $ticket) {
                if (!$user->hasRole('super-admin') && !$user->hasRole('admin') && $ticket->assigned_to != $user->id) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => "You don't have permission to modify ticket #{$ticket->id}"
                    ], 403);
                }
            }

            // Perform bulk update
            $updatedCount = Ticket::whereIn('id', $ticketIds)->update([
                'type_id' => $typeId,
                'updated_at' => now()
            ]);

            // Get category name
            $type = TicketsType::find($typeId);

            // Create notifications for assigned users
            foreach ($tickets as $ticket) {
                if ($ticket->assigned_to) {
                    \App\Notification::create([
                        'user_id' => $ticket->assigned_to,
                        'title' => 'Ticket Category Updated',
                        'message' => "Ticket #{$ticket->id} category has been changed to {$type->name} by " . $user->name,
                        'type' => 'ticket_category_changed',
                        'related_id' => $ticket->id,
                        'is_read' => 0
                    ]);
                }
            }

            DB::commit();

            Log::info("Bulk category update: User {$user->id} updated {$updatedCount} tickets to category {$typeId}");

            return response()->json([
                'success' => true,
                'message' => "Successfully updated category of {$updatedCount} ticket(s) to {$type->name}",
                'updated_count' => $updatedCount
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bulk category update error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating ticket category: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk delete tickets
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ticket_ids' => 'required|array|min:1',
            'ticket_ids.*' => 'exists:tickets,id',
        ]);

        try {
            DB::beginTransaction();

            $ticketIds = $request->ticket_ids;
            $user = Auth::user();

            // Only super-admin and admin can bulk delete
            if (!$user->hasRole('super-admin') && !$user->hasRole('admin')) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to delete tickets'
                ], 403);
            }

            // Get tickets for logging
            $tickets = Ticket::whereIn('id', $ticketIds)->get();

            // Soft delete tickets (if using soft deletes)
            $deletedCount = Ticket::whereIn('id', $ticketIds)->delete();

            DB::commit();

            Log::warning("Bulk delete: User {$user->id} deleted {$deletedCount} tickets: " . implode(', ', $ticketIds));

            return response()->json([
                'success' => true,
                'message' => "Successfully deleted {$deletedCount} ticket(s)",
                'deleted_count' => $deletedCount
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bulk delete error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting tickets: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get bulk operation options (users, statuses, priorities, categories)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBulkOptions()
    {
        try {
            $users = User::select('id', 'name', 'email')
                        ->where('active', 1)
                        ->orderBy('name')
                        ->get();

            $statuses = TicketsStatus::select('id', 'name', 'color')
                                    ->orderBy('name')
                                    ->get();

            $priorities = TicketsPriority::select('id', 'name', 'color')
                                        ->orderBy('id')
                                        ->get();

            $types = TicketsType::select('id', 'name')
                                ->orderBy('name')
                                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'users' => $users,
                    'statuses' => $statuses,
                    'priorities' => $priorities,
                    'types' => $types
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Get bulk options error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching options: ' . $e->getMessage()
            ], 500);
        }
    }
}
