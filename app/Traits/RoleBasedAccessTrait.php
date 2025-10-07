<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

trait RoleBasedAccessTrait
{
    /**
     * Check if user has required roles
     */
    protected function hasAnyRole(array $roles): bool
    {
        $user = Auth::user();
        return $user && $user->hasAnyRole($roles);
    }

    /**
     * Check if user has specific role
     */
    protected function hasRole(string $role): bool
    {
        $user = Auth::user();
        return $user && $user->hasRole($role);
    }

    /**
     * Get role-based query filters
     */
    protected function applyRoleBasedFilters($query, $user = null)
    {
        $user = $user ?? Auth::user();
        
        if (!$user) {
            return $query->whereRaw('1 = 0'); // Return empty result
        }

        if ($user->hasRole('user')) {
            // Users can only see their own records
            return $query->where('user_id', $user->id);
        } elseif ($user->hasRole('admin')) {
            // Admins can see records assigned to them or unassigned
            return $query->where(function($q) use ($user) {
                $q->where('assigned_to', $user->id)
                  ->orWhereNull('assigned_to');
            });
        } elseif ($user->hasAnyRole(['super-admin', 'management'])) {
            // Super admins and management can see everything
            return $query;
        }

        return $query->whereRaw('1 = 0'); // Default to empty if no role matches
    }

    /**
     * Check if user can perform action on resource
     */
    protected function canPerformAction(string $action, $resource = null): bool
    {
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }

        switch ($action) {
            case 'create':
                return $user->hasAnyRole(['admin', 'super-admin', 'user']);
            
            case 'view':
                if ($resource && method_exists($resource, 'user_id')) {
                    return $user->hasAnyRole(['super-admin', 'management']) || 
                           $user->hasRole('admin') || 
                           $resource->user_id === $user->id;
                }
                return $user->hasAnyRole(['admin', 'super-admin', 'management', 'user']);
            
            case 'edit':
            case 'update':
                if ($resource && method_exists($resource, 'user_id')) {
                    return $user->hasAnyRole(['super-admin', 'admin']) || 
                           ($user->hasRole('user') && $resource->user_id === $user->id);
                }
                return $user->hasAnyRole(['admin', 'super-admin']);
            
            case 'delete':
                return $user->hasAnyRole(['super-admin', 'admin']);
            
            case 'assign':
                return $user->hasAnyRole(['super-admin', 'admin']);
            
            default:
                return false;
        }
    }

    /**
     * Abort if user cannot perform action
     */
    protected function authorizeAction(string $action, $resource = null)
    {
        if (!$this->canPerformAction($action, $resource)) {
            abort(403, 'You do not have permission to ' . $action . ' this resource.');
        }
    }
}