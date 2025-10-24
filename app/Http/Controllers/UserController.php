<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin|super-admin');
    }

    /**
     * Display a listing of users
     */
    public function index()
    {
        $users = User::with('roles')->paginate(15);
        $roles = Role::all();
        
        return view('users.index', compact('users', 'roles'));
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        $this->authorize('create-users');
        
        $roles = Role::all();
        
        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        $this->authorize('create-users');
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|exists:roles,name'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole($request->role);

        return redirect()->route('users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified user
     */
    public function show(User $user)
    {
        $user->load('roles', 'permissions');
        $userActivities = []; // Placeholder for user activities
        
        return view('users.show', compact('user', 'userActivities'));
    }

    /**
     * Show the form for editing the specified user
     */
    public function edit(User $user)
    {
        $this->authorize('edit-users');
        
        $roles = Role::all();
        
        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, User $user)
    {
        $this->authorize('edit-users');
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|exists:roles,name'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        if ($request->filled('password')) {
            $user->update([
                'password' => Hash::make($request->password)
            ]);
        }

        // Update role
        $user->syncRoles([$request->role]);

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user
     */
    public function destroy(User $user)
    {
        $this->authorize('delete-users');
        
        // Prevent deleting the current user
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully.');
    }

    /**
     * Show user roles management
     */
    public function roles()
    {
        $roles = Role::withCount('users')->get();
        $users = User::with('roles')->get();
        
        return view('users.roles', compact('roles', 'users'));
    }

    /**
     * Get user performance data for API
     */
    public function getPerformance(User $user, Request $request)
    {
        $days = $request->get('days', 30);
        
        // Get user performance metrics (placeholder implementation)
        $performance = [
            'tickets_resolved' => 0,
            'assets_managed' => 0,
            'daily_activities' => 0,
            'efficiency_score' => 0
        ];
        
        return response()->json($performance);
    }

    /**
     * Get user workload data for API
     */
    public function getWorkload(User $user)
    {
        // Get user workload (placeholder implementation)
        $workload = [
            'active_tickets' => 0,
            'pending_activities' => 0,
            'assigned_assets' => 0,
            'workload_score' => 0
        ];
        
        return response()->json($workload);
    }

    /**
     * Get user activities for API
     */
    public function getActivities(User $user, Request $request)
    {
        $limit = $request->get('limit', 10);
        
        // Get user activities (placeholder implementation)
        $activities = [];
        
        return response()->json($activities);
    }
}