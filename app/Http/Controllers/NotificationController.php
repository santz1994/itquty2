<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Notification;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->middleware('auth');
        $this->notificationService = $notificationService;
    }

    /**
     * Display user's notifications
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = Notification::forUser($user->id)
                            ->orderBy('created_at', 'desc');

        // Filter by type if specified
        if ($request->has('type') && $request->type) {
            $query->byType($request->type);
        }

        // Filter by status if specified
        if ($request->has('status')) {
            if ($request->status === 'unread') {
                $query->unread();
            } elseif ($request->status === 'read') {
                $query->read();
            }
        }

        $notifications = $query->paginate(20);
        
        // Get summary data
        $summary = $this->notificationService->getUserSummary($user->id);

        return view('notifications.index', compact('notifications', 'summary'));
    }

    /**
     * Get unread notifications count (AJAX)
     */
    public function getUnreadCount(): JsonResponse
    {
        $count = Notification::getUnreadCountForUser(Auth::id());
        return response()->json(['count' => $count]);
    }

    /**
     * Get recent notifications (AJAX)
     */
    public function getRecent(Request $request): JsonResponse
    {
        $limit = $request->get('limit', 5);
        $notifications = Notification::getRecentForUser(Auth::id(), $limit);
        
        return response()->json([
            'notifications' => $notifications->map(function($notification) {
                return [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'type' => $notification->type,
                    'priority' => $notification->priority,
                    'is_read' => $notification->is_read,
                    'time_ago' => $notification->time_ago,
                    'icon_class' => $notification->icon_class,
                    'action_url' => $notification->action_url,
                ];
            })
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markRead(Notification $notification): JsonResponse
    {
        // Check if user owns this notification
        if ($notification->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read'
        ]);
    }

    /**
     * Mark notification as unread
     */
    public function markUnread(Notification $notification): JsonResponse
    {
        // Check if user owns this notification
        if ($notification->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $notification->markAsUnread();

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as unread'
        ]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllRead(): JsonResponse
    {
        $count = Notification::markAllAsReadForUser(Auth::id());

        return response()->json([
            'success' => true,
            'message' => "Marked {$count} notifications as read"
        ]);
    }

    /**
     * Delete notification
     */
    public function destroy(Notification $notification): JsonResponse
    {
        // Check if user owns this notification
        if ($notification->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notification deleted'
        ]);
    }

    /**
     * Show notification details
     */
    public function show(Notification $notification)
    {
        // Check if user owns this notification
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }

        // Mark as read if not already
        if (!$notification->is_read) {
            $notification->markAsRead();
        }

        // If there's an action URL, redirect to it
        if ($notification->action_url) {
            return redirect($notification->action_url);
        }

        return view('notifications.show', compact('notification'));
    }

    /**
     * Admin: View all notifications statistics
     */
    public function admin()
    {
        $this->authorize('view-notifications');
        
        $statistics = $this->notificationService->getStatistics();
        
        $recentNotifications = Notification::with('user')
                                         ->orderBy('created_at', 'desc')
                                         ->limit(20)
                                         ->get();

        return view('admin.notifications', compact('statistics', 'recentNotifications'));
    }

    /**
     * Admin: Run automatic notification checks
     */
    public function runChecks(): JsonResponse
    {
        $this->authorize('manage-notifications');
        
        $results = $this->notificationService->runAutomaticChecks();

        return response()->json([
            'success' => true,
            'message' => 'Automatic checks completed',
            'results' => $results
        ]);
    }

    /**
     * Admin: Send test notification
     */
    public function sendTest(Request $request): JsonResponse
    {
        $this->authorize('manage-notifications');
        
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'priority' => 'required|in:low,normal,high,urgent',
            'user_ids' => 'array',
            'user_ids.*' => 'exists:users,id'
        ]);

        $userIds = $request->user_ids ?? [Auth::id()];
        
        $notifications = Notification::createSystemAlert(
            $request->title,
            $request->message,
            $userIds,
            $request->priority
        );

        return response()->json([
            'success' => true,
            'message' => 'Test notification sent to ' . count($notifications) . ' users'
        ]);
    }
}
