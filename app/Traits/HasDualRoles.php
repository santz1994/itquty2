<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Traits\HasRoles as SpatieHasRoles;

/**
 * HasDualRoles trait that extends Spatie's HasRoles trait
 * to keep both role systems in sync (legacy role_user table and Spatie's model_has_roles)
 */
trait HasDualRoles
{
    use SpatieHasRoles {
        SpatieHasRoles::hasRole as protected spatieHasRole;
        SpatieHasRoles::hasAnyRole as protected spatieHasAnyRole;
        SpatieHasRoles::hasAllRoles as protected spatieHasAllRoles;
        assignRole as protected spatieAssignRole;
        removeRole as protected spatieRemoveRole;
        syncRoles as protected spatieSyncRoles;
    }

    /**
     * Check if user has any of the given roles
     * 
     * @param string|array|\Spatie\Permission\Models\Role|\Illuminate\Database\Eloquent\Collection $roles
     */
    public function hasAnyRole($roles)
    {
        // Get user roles from database to avoid cached issues
        $roleNames = $this->getRoleNamesFromDB();
        
        foreach ((array)$roles as $role) {
            if (in_array($role, $roleNames)) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Check if user has a specific role (compatible with Spatie and legacy)
     * 
     * @param string|array|\Spatie\Permission\Models\Role|\Illuminate\Database\Eloquent\Collection $roles
     */
    public function hasRole($roles)
    {
        // Get user roles from database to avoid cached issues
        $roleNames = $this->getRoleNamesFromDB();
        
        // If roles is a string (single role)
        if (is_string($roles)) {
            return in_array($roles, $roleNames);
        }
        
        // If roles is an array (multiple roles - ANY of them)
        if (is_array($roles)) {
            foreach ($roles as $role) {
                if (in_array($role, $roleNames)) {
                    return true;
                }
            }
            return false;
        }
        
        // Handle object or collection types
        if (is_object($roles)) {
            // If it's a collection, convert to array and check any match
            if (method_exists($roles, 'toArray')) {
                $rolesArray = $roles->toArray();
                foreach ($rolesArray as $role) {
                    $roleName = is_string($role) ? $role : (is_object($role) && isset($role->name) ? $role->name : '');
                    if ($roleName && in_array($roleName, $roleNames)) {
                        return true;
                    }
                }
            } else {
                // Single role object, try to get the name
                $roleName = isset($roles->name) ? $roles->name : (string)$roles;
                return in_array($roleName, $roleNames);
            }
        }
        
        return false;
    }
    
    /**
     * Get role names directly from database to avoid cache issues
     */
    protected function getRoleNamesFromDB()
    {
        return DB::table('model_has_roles')
            ->where('model_id', $this->id)
            ->where('model_type', get_class($this))
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->pluck('roles.name')
            ->toArray();
    }

    /**
     * Override assignRole to also update the legacy role_user table
     */
    public function assignRole(...$roles)
    {
        $result = $this->spatieAssignRole(...$roles);
        $this->syncLegacyRoles();
        return $result;
    }

    /**
     * Override removeRole to also update the legacy role_user table
     */
    public function removeRole($role)
    {
        $result = $this->spatieRemoveRole($role);
        $this->syncLegacyRoles();
        return $result;
    }

    /**
     * Override syncRoles to also update the legacy role_user table
     */
    public function syncRoles(...$roles)
    {
        $result = $this->spatieSyncRoles(...$roles);
        $this->syncLegacyRoles();
        return $result;
    }

    /**
     * Sync roles from Spatie to legacy role_user table
     */
    protected function syncLegacyRoles()
    {
        if (!Schema::hasTable('role_user')) {
            return;
        }

        $spatieRoleIds = DB::table('model_has_roles')
            ->where('model_id', $this->id)
            ->where('model_type', get_class($this))
            ->pluck('role_id')
            ->toArray();

        DB::table('role_user')
            ->where('user_id', $this->id)
            ->delete();

        foreach ($spatieRoleIds as $roleId) {
            DB::table('role_user')->insert([
                'user_id' => $this->id,
                'role_id' => $roleId
            ]);
        }
    }
}