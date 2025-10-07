<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CreateDailyActivityRequest;
use App\DailyActivity;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DailyActivityController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin|super-admin|management');
    }

    /**
     * Display a listing of daily activities
     */
    public function index(Request $request)
    {
        $query = DailyActivity::with(['user']);

        // Filter by date range
        if ($request->has('date_from') && $request->date_from !== '') {
            $query->whereDate('activity_date', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to !== '') {
            $query->whereDate('activity_date', '<=', $request->date_to);
        }

        // Filter by user (admin only)
        if ($request->has('user_id') && $request->user_id !== '' && Auth::user()->hasRole('admin')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by activity type
        if ($request->has('activity_type') && $request->activity_type !== '') {
            $query->where('type', $request->activity_type);
        }

        // If regular user, only show their activities
        if (!Auth::user()->hasRole(['admin', 'super_admin'])) {
            $query->where('user_id', Auth::id());
        }

        $activities = $query->orderBy('activity_date', 'desc')
                           ->orderBy('created_at', 'desc')
                           ->paginate(20);

        // Get filter options
        $users = Auth::user()->hasRole(['admin', 'super_admin']) ? User::all() : collect();
        $activityTypes = DailyActivity::select('type as activity_type')
                                     ->distinct()
                                     ->pluck('activity_type');

        return view('daily-activities.index', compact('activities', 'users', 'activityTypes'));
    }

    /**
     * Show the form for creating a new daily activity
     */
    public function create()
    {
        $today = Carbon::today()->toDateString();
        $pageTitle = 'Create Daily Activity';
        
        return view('daily-activities.create', compact('today', 'pageTitle'));
    }

    /**
     * Store a newly created daily activity
     */
    public function store(CreateDailyActivityRequest $request)
    {
        try {
            $data = $request->validated();
            $data['user_id'] = Auth::id();

            DailyActivity::create($data);
            
            return redirect()->route('daily-activities.index')
                           ->with('success', 'Aktivitas harian berhasil dicatat');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal mencatat aktivitas: ' . $e->getMessage()])
                         ->withInput();
        }
    }

    /**
     * Display the specified daily activity
     */
    public function show(DailyActivity $dailyActivity)
    {
        // Check if user can view this activity
        if (!Auth::user()->hasRole(['admin', 'super_admin']) && $dailyActivity->user_id !== Auth::id()) {
            abort(403, 'Tidak memiliki akses untuk melihat aktivitas ini');
        }

        $dailyActivity->load('user');
        
        return view('daily-activities.show', compact('dailyActivity'));
    }

    /**
     * Show the form for editing the specified daily activity
     */
    public function edit(DailyActivity $dailyActivity)
    {
        // Check if user can edit this activity
        if (!Auth::user()->hasRole(['admin', 'super_admin']) && $dailyActivity->user_id !== Auth::id()) {
            abort(403, 'Tidak memiliki akses untuk mengedit aktivitas ini');
        }

        // Only allow editing activities from today or yesterday
        $activityDate = Carbon::parse($dailyActivity->activity_date);
        $cutoffDate = Carbon::yesterday();
        
        if ($activityDate->lt($cutoffDate) && !Auth::user()->hasRole(['admin', 'super_admin'])) {
            return back()->with('error', 'Hanya dapat mengedit aktivitas hari ini atau kemarin');
        }

        return view('daily-activities.edit', compact('dailyActivity'));
    }

    /**
     * Update the specified daily activity
     */
    public function update(CreateDailyActivityRequest $request, DailyActivity $dailyActivity)
    {
        // Check if user can update this activity
        if (!Auth::user()->hasRole(['admin', 'super_admin']) && $dailyActivity->user_id !== Auth::id()) {
            abort(403, 'Tidak memiliki akses untuk mengupdate aktivitas ini');
        }

        try {
            $data = $request->validated();
            
            // Don't allow changing user_id unless admin
            if (!Auth::user()->hasRole(['admin', 'super_admin'])) {
                unset($data['user_id']);
            }

            $dailyActivity->update($data);
            
            return redirect()->route('daily-activities.show', $dailyActivity->id)
                           ->with('success', 'Aktivitas harian berhasil diperbarui');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal memperbarui aktivitas: ' . $e->getMessage()])
                         ->withInput();
        }
    }

    /**
     * Remove the specified daily activity
     */
    public function destroy(DailyActivity $dailyActivity)
    {
        // Check if user can delete this activity
        if (!Auth::user()->hasRole(['admin', 'super_admin']) && $dailyActivity->user_id !== Auth::id()) {
            abort(403, 'Tidak memiliki akses untuk menghapus aktivitas ini');
        }

        try {
            $dailyActivity->delete();
            
            return redirect()->route('daily-activities.index')
                           ->with('success', 'Aktivitas harian berhasil dihapus');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus aktivitas: ' . $e->getMessage());
        }
    }

    /**
     * Show today's activities
     */
    public function today()
    {
        $today = Carbon::today();
        
        $query = DailyActivity::with(['user'])
                             ->whereDate('activity_date', $today);

        // If regular user, only show their activities
        if (!Auth::user()->hasRole(['admin', 'super_admin'])) {
            $query->where('user_id', Auth::id());
        }

        $activities = $query->orderBy('created_at', 'desc')->get();

        return view('daily-activities.today', compact('activities', 'today'));
    }

    /**
     * Show weekly summary
     */
    public function weekly()
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();
        
        $query = DailyActivity::with(['user'])
                             ->whereBetween('activity_date', [$startOfWeek, $endOfWeek]);

        // If regular user, only show their activities
        if (!Auth::user()->hasRole(['admin', 'super_admin'])) {
            $query->where('user_id', Auth::id());
        }

        $activities = $query->orderBy('activity_date', 'desc')
                           ->orderBy('created_at', 'desc')
                           ->get();

        // Group by date for better display
        $groupedActivities = $activities->groupBy(function($activity) {
            return Carbon::parse($activity->activity_date)->format('Y-m-d');
        });

        return view('daily-activities.weekly', compact('groupedActivities', 'startOfWeek', 'endOfWeek'));
    }

    /**
     * Export activities to CSV
     */
    public function export(Request $request)
    {
        $query = DailyActivity::with(['user']);

        // Apply filters
        if ($request->has('date_from') && $request->date_from !== '') {
            $query->whereDate('activity_date', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to !== '') {
            $query->whereDate('activity_date', '<=', $request->date_to);
        }

        if ($request->has('user_id') && $request->user_id !== '' && Auth::user()->hasRole('admin')) {
            $query->where('user_id', $request->user_id);
        }

        // If regular user, only export their activities
        if (!Auth::user()->hasRole(['admin', 'super_admin'])) {
            $query->where('user_id', Auth::id());
        }

        $activities = $query->orderBy('activity_date', 'desc')->get();

        // Generate CSV
        $filename = 'daily-activities-' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($activities) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, ['Tanggal', 'User', 'Tipe Aktivitas', 'Deskripsi', 'Durasi (menit)', 'Catatan']);
            
            foreach ($activities as $activity) {
                fputcsv($file, [
                    $activity->activity_date,
                    $activity->user->name,
                    $activity->type,
                    $activity->description,
                    $activity->duration_minutes,
                    $activity->notes
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Display calendar view for daily activities
     */
    public function calendar(Request $request)
    {
        // Get users for filter (admin only)
        $users = Auth::user()->hasRole(['admin', 'super-admin']) ? User::all() : collect();

        return view('daily-activities.calendar', compact('users'));
    }

    /**
     * Get activities for calendar (JSON API)
     */
    public function getCalendarEvents(Request $request)
    {
        $query = DailyActivity::with(['user']);

        // Filter by user
        if ($request->has('user_id') && $request->user_id !== '') {
            if (Auth::user()->hasRole(['admin', 'super-admin'])) {
                $query->where('user_id', $request->user_id);
            }
        }

        // If regular user, only show their activities
        if (!Auth::user()->hasRole(['admin', 'super-admin'])) {
            $query->where('user_id', Auth::id());
        }

        // Filter by date range if provided
        if ($request->has('start')) {
            $query->whereDate('activity_date', '>=', $request->start);
        }

        if ($request->has('end')) {
            $query->whereDate('activity_date', '<=', $request->end);
        }

        $activities = $query->get();

        // Format for FullCalendar
        $events = [];
        foreach ($activities as $activity) {
            $events[] = [
                'id' => $activity->id,
                'title' => $activity->description,
                'start' => $activity->activity_date,
                'backgroundColor' => $this->getActivityColor($activity->type),
                'borderColor' => $this->getActivityColor($activity->type),
                'extendedProps' => [
                    'user_name' => $activity->user->name,
                    'activity_type' => $activity->type,
                    'duration_minutes' => $activity->duration_minutes,
                    'notes' => $activity->notes,
                    'description' => $activity->description,
                    'icon' => $this->getActivityIcon($activity->type)
                ]
            ];
        }

        return response()->json($events);
    }

    /**
     * Get activity details for specific date
     */
    public function getDateActivities(Request $request)
    {
        $date = $request->get('date');
        
        $query = DailyActivity::with(['user'])
                             ->whereDate('activity_date', $date);

        // Filter by user for admin
        if ($request->has('user_id') && $request->user_id !== '' && Auth::user()->hasRole(['admin', 'super-admin'])) {
            $query->where('user_id', $request->user_id);
        }

        // If regular user, only show their activities
        if (!Auth::user()->hasRole(['admin', 'super-admin'])) {
            $query->where('user_id', Auth::id());
        }

        $activities = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'date' => Carbon::parse($date)->format('d F Y'),
            'activities' => $activities->map(function($activity) {
                return [
                    'id' => $activity->id,
                    'user_name' => $activity->user->name,
                    'activity_type' => $activity->type,
                    'description' => $activity->description,
                    'duration_minutes' => $activity->duration_minutes,
                    'notes' => $activity->notes,
                    'created_at' => $activity->created_at->format('H:i'),
                    'icon' => $this->getActivityIcon($activity->type)
                ];
            })
        ]);
    }

    /**
     * Get color for activity type
     */
    private function getActivityColor($activityType)
    {
        $colors = [
            'maintenance' => '#f39c12',      // Orange
            'installation' => '#3c8dbc',     // Blue
            'support' => '#00a65a',          // Green
            'training' => '#605ca8',         // Purple
            'meeting' => '#dd4b39',          // Red
            'documentation' => '#00c0ef',    // Light Blue
            'repair' => '#e74c3c',           // Dark Red
            'upgrade' => '#9b59b6',          // Violet
            'monitoring' => '#34495e',       // Dark Gray
            'other' => '#95a5a6'             // Gray
        ];

        return $colors[$activityType] ?? '#95a5a6';
    }

    /**
     * Get icon for activity type
     */
    private function getActivityIcon($activityType)
    {
        $icons = [
            'maintenance' => 'fa fa-wrench',
            'installation' => 'fa fa-download',
            'support' => 'fa fa-support',
            'training' => 'fa fa-graduation-cap',
            'meeting' => 'fa fa-users',
            'documentation' => 'fa fa-file-text',
            'repair' => 'fa fa-tools',
            'upgrade' => 'fa fa-arrow-up',
            'monitoring' => 'fa fa-eye',
            'other' => 'fa fa-cog'
        ];

        return $icons[$activityType] ?? 'fa fa-cog';
    }
}