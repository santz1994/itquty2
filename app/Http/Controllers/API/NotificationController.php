<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display a listing of notifications for authenticated user
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = Notification::where('user_id', auth()->user()->id)
                            ->with(['relatedUser', 'relatedTicket', 'relatedAsset']);

        // Apply filters
        if ($request->has('is_read')) {
            $query->where('is_read', $request->boolean('is_read'));
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }

        // Date filters
        if ($request->has('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->where('created_at', '<=', $request->date_to);
        }

        // Order by priority and date
        $query->orderByRaw("FIELD(priority, 'urgent', 'high', 'normal', 'low')")
              ->orderBy('created_at', 'desc');

        // Pagination
        $perPage = $request->get('per_page', 15);
        $notifications = $query->paginate($perPage);

        // Transform data
        $notifications->getCollection()->transform(function ($notification) {
            return $this->transformNotification($notification);
        });

        return response()->json([
            'success' => true,
            'data' => $notifications,
            'message' => 'Notifications retrieved successfully'
        ]);
    }

    /**
     * Display the specified notification
     *
     * @param Notification $notification
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Notification $notification)
    {
        // Check if notification belongs to authenticated user
        if ($notification->user_id !== auth()->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to access this notification'
            ], 403);
        }

        $notification->load(['relatedUser', 'relatedTicket', 'relatedAsset']);

        // Mark as read when viewed
        if (!$notification->is_read) {
            $notification->markAsRead();
        }

        return response()->json([
            'success' => true,
            'data' => $this->transformNotification($notification, true),
            'message' => 'Notification retrieved successfully'
        ]);
    }

    /**
     * Mark notification as read
     *
     * @param Notification $notification
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsRead(Notification $notification)
    {
        // Check if notification belongs to authenticated user
        if ($notification->user_id !== auth()->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to modify this notification'
            ], 403);
        }

        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'data' => $this->transformNotification($notification),
            'message' => 'Notification marked as read'
        ]);
    }

    /**
     * Mark all notifications as read for authenticated user
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAllAsRead()
    {
        $updated = Notification::where('user_id', auth()->user()->id)
                              ->where('is_read', false)
                              ->update([
                                  'is_read' => true,
                                  'read_at' => now()
                              ]);

        return response()->json([
            'success' => true,
            'data' => [
                'updated_count' => $updated
            ],
            'message' => 'All notifications marked as read'
        ]);
    }

    /**
     * Get unread notification count
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUnreadCount()
    {
        $count = Notification::where('user_id', auth()->user()->id)
                            ->where('is_read', false)
                            ->count();

        return response()->json([
            'success' => true,
            'data' => [
                'unread_count' => $count
            ],
            'message' => 'Unread count retrieved successfully'
        ]);
    }

    /**
     * Delete notification
     *
     * @param Notification $notification
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Notification $notification)
    {
        // Check if notification belongs to authenticated user
        if ($notification->user_id !== auth()->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to delete this notification'
            ], 403);
        }

        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notification deleted successfully'
        ]);
    }

    /**
     * Get notification statistics for authenticated user
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStats()
    {
        $userId = auth()->user()->id;
        
        $stats = [
            'total' => Notification::where('user_id', $userId)->count(),
            'unread' => Notification::where('user_id', $userId)->where('is_read', false)->count(),
            'read' => Notification::where('user_id', $userId)->where('is_read', true)->count(),
            'by_type' => Notification::where('user_id', $userId)
                                   ->selectRaw('type, COUNT(*) as count')
                                   ->groupBy('type')
                                   ->pluck('count', 'type'),
            'by_priority' => Notification::where('user_id', $userId)
                                        ->selectRaw('priority, COUNT(*) as count')
                                        ->groupBy('priority')
                                        ->pluck('count', 'priority'),
            'recent_count' => Notification::where('user_id', $userId)
                                        ->where('created_at', '>=', now()->subDays(7))
                                        ->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
            'message' => 'Notification statistics retrieved successfully'
        ]);
    }

    /**
     * Transform notification data
     *
     * @param Notification $notification
     * @param bool $detailed
     * @return array
     */
    private function transformNotification(Notification $notification, $detailed = false)
    {
        $data = [
            'id' => $notification->id,
            'title' => $notification->title,
            'message' => $notification->message,
            'type' => $notification->type,
            'priority' => $notification->priority,
            'is_read' => $notification->is_read,
            'read_at' => $notification->read_at,
            'type_badge' => $notification->type_badge,
            'priority_color' => $notification->priority_color,
            'formatted_time_ago' => $notification->formatted_time_ago,
            'created_at' => $notification->created_at,
            'updated_at' => $notification->updated_at
        ];

        if ($detailed) {
            // Add related data if available
            if ($notification->relatedUser) {
                $data['related_user'] = [
                    'id' => $notification->relatedUser->id,
                    'name' => $notification->relatedUser->name,
                    'email' => $notification->relatedUser->email
                ];
            }

            if ($notification->relatedTicket) {
                $data['related_ticket'] = [
                    'id' => $notification->relatedTicket->id,
                    'title' => $notification->relatedTicket->title,
                    'status' => $notification->relatedTicket->status->name ?? null
                ];
            }

            if ($notification->relatedAsset) {
                $data['related_asset'] = [
                    'id' => $notification->relatedAsset->id,
                    'asset_tag' => $notification->relatedAsset->asset_tag,
                    'name' => $notification->relatedAsset->name
                ];
            }

            $data['action_data'] = $notification->action_data;
        }

        return $data;
    }
}