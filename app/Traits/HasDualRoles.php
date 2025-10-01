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
        assignRole as protected spatieAssignRole;
        removeRole as protected spatieRemoveRole;
        syncRoles as protected spatieSyncRoles;
    }

    /**
     * Check if user has any of the given roles
     */
    public function hasAnyRole($roles)
    {
        $roleNames = $this->getRoleNames()->toArray();
        foreach ((array)$roles as $role) {
            if (in_array($role, $roleNames)) {
                return true;
            }
        }
        return false;
    }
    
        /**
         * Check if user has a specific role (compatible with Spatie and legacy)
         */
        public function hasRole($role)
        {
            $roleNames = $this->getRoleNames()->toArray();
            return in_array($role, $roleNames);
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