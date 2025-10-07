<?php

namespace App\Services;

use App\User;
use App\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UserService
{
    /**
     * Create new user with role assignment
     */
    public function createUser(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Create user
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'employee_num' => $data['employee_num'] ?? null,
                'division_id' => $data['division_id'] ?? null,
                'position' => $data['position'] ?? null,
                'phone' => $data['phone'] ?? null,
                'activated' => $data['activated'] ?? true,
            ]);

            // Assign role if provided
            if (isset($data['role_id'])) {
                $role = Role::findOrFail($data['role_id']);
                $user->assignRole($role);
            }

            return $user->load('roles', 'division');
        });
    }

    /**
     * Update user information
     */
    public function updateUser(User $user, array $data)
    {
        return DB::transaction(function () use ($user, $data) {
            // Update basic info
            $updateData = array_filter([
                'name' => $data['name'] ?? null,
                'email' => $data['email'] ?? null,
                'employee_num' => $data['employee_num'] ?? null,
                'division_id' => $data['division_id'] ?? null,
                'position' => $data['position'] ?? null,
                'phone' => $data['phone'] ?? null,
                'activated' => $data['activated'] ?? null,
            ], function($value) {
                return $value !== null;
            });

            if (isset($data['password']) && !empty($data['password'])) {
                $updateData['password'] = Hash::make($data['password']);
            }

            $user->update($updateData);

            // Update roles if provided
            if (isset($data['role_id'])) {
                $role = Role::findOrFail($data['role_id']);
                $user->syncRoles([$role]);
            }

            return $user->fresh(['roles', 'division']);
        });
    }

    /**
     * Assign role to user
     */
    public function assignRole(User $user, int $roleId)
    {
        $role = Role::findOrFail($roleId);
        $user->assignRole($role);
        
        return $user->fresh('roles');
    }

    /**
     * Remove role from user
     */
    public function removeRole(User $user, int $roleId)
    {
        $role = Role::findOrFail($roleId);
        $user->removeRole($role);
        
        return $user->fresh('roles');
    }

    /**
     * Send email reminder to user
     */
    public function sendEmailReminder(User $user, string $subject = 'Your Reminder!', string $template = 'emails.reminder')
    {
        try {
            Mail::send($template, ['user' => $user], function ($m) use ($user, $subject) {
                $m->from(config('mail.from.address'), config('mail.from.name'));
                $m->to($user->email, $user->name)->subject($subject);
            });
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send email reminder to user ' . $user->id . ': ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get users with their roles and statistics
     */
    public function getUsersWithStats()
    {
        return User::with(['roles', 'division'])
                   ->withCount(['assets', 'tickets'])
                   ->paginate(20);
    }

    /**
     * Search users by various criteria
     */
    public function searchUsers(string $search)
    {
        return User::with(['roles', 'division'])
                   ->where(function($query) use ($search) {
                       $query->where('name', 'like', "%{$search}%")
                             ->orWhere('email', 'like', "%{$search}%")
                             ->orWhere('employee_num', 'like', "%{$search}%");
                   })
                   ->paginate(20);
    }

    /**
     * Get users by role
     */
    public function getUsersByRole(string $roleName)
    {
        return User::role($roleName)
                   ->with(['roles', 'division'])
                   ->get();
    }

    /**
     * Activate/Deactivate user
     */
    public function toggleUserStatus(User $user)
    {
        $user->update(['activated' => !$user->activated]);
        return $user;
    }

    /**
     * Reset user password
     */
    public function resetUserPassword(User $user, string $newPassword = null)
    {
        $password = $newPassword ?? Str::random(8);
        
        $user->update([
            'password' => Hash::make($password)
        ]);

        // Send password reset email
        $this->sendPasswordResetEmail($user, $password);
        
        return $password;
    }

    /**
     * Send password reset email
     */
    private function sendPasswordResetEmail(User $user, string $newPassword)
    {
        try {
            Mail::send('emails.password-reset', [
                'user' => $user, 
                'password' => $newPassword
            ], function ($m) use ($user) {
                $m->from(config('mail.from.address'), config('mail.from.name'));
                $m->to($user->email, $user->name)->subject('Password Reset');
            });
        } catch (\Exception $e) {
            Log::error('Failed to send password reset email to user ' . $user->id . ': ' . $e->getMessage());
        }
    }

    /**
     * Update user with role validation (prevents removing last super admin)
     */
    public function updateUserWithRoleValidation(User $user, array $data)
    {
        return DB::transaction(function () use ($user, $data) {
            // Check if changing role would leave no super admins
            if (isset($data['role_id'])) {
                $currentUserRole = $user->roles->first();
                $newRole = Role::findOrFail($data['role_id']);
                
                if ($currentUserRole && $currentUserRole->name === 'super-admin' && $newRole->name !== 'super-admin') {
                    $superAdminCount = User::role('super-admin')->count();
                    if ($superAdminCount <= 1) {
                        throw new \Exception('Cannot change role as there must be one (1) or more users with the role of Super Administrator.');
                    }
                }
            }
            
            return $this->updateUser($user, $data);
        });
    }

    /**
     * Get user dashboard statistics
     */
    public function getUserDashboardStats(User $user)
    {
        return [
            'total_assets' => $user->assets()->count(),
            'active_tickets' => $user->tickets()->whereHas('ticket_status', function($q) {
                $q->whereNotIn('name', ['Closed', 'Resolved']);
            })->count(),
            'completed_tickets' => $user->tickets()->whereHas('ticket_status', function($q) {
                $q->whereIn('name', ['Closed', 'Resolved']);
            })->count(),
            'pending_requests' => $user->assetRequests()->where('status', 'pending')->count(),
        ];
    }
}