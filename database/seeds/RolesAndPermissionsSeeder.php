<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Asset Management
            'view-assets',
            'create-assets',
            'edit-assets',
            'delete-assets',
            'export-assets',
            'import-assets',
            
            // Ticket Management
            'view-tickets',
            'create-tickets',
            'edit-tickets',
            'delete-tickets',
            'assign-tickets',
            'export-tickets',
            
            // User Management
            'view-users',
            'create-users',
            'edit-users',
            'delete-users',
            'manage-roles',
            
            // Daily Activities
            'view-daily-activities',
            'create-daily-activities',
            'edit-daily-activities',
            'delete-daily-activities',
            
            // Reports & Analytics
            'view-reports',
            'view-dashboard',
            'view-kpi-dashboard',
            
            // System Settings
            'manage-settings',
            'manage-locations',
            'manage-divisions',
            'manage-suppliers',
            'manage-manufacturers',
            
            // Asset Requests
            'view-asset-requests',
            'create-asset-requests',
            'approve-asset-requests',
            'reject-asset-requests',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        
        // Super Admin Role
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        $superAdmin->syncPermissions(Permission::all());

        // Admin Role
        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $admin->syncPermissions([
            'view-assets', 'create-assets', 'edit-assets', 'export-assets', 'import-assets',
            'view-tickets', 'create-tickets', 'edit-tickets', 'assign-tickets', 'export-tickets',
            'view-users', 'create-users', 'edit-users',
            'view-daily-activities', 'create-daily-activities', 'edit-daily-activities',
            'view-reports', 'view-dashboard',
            'manage-locations', 'manage-divisions', 'manage-suppliers', 'manage-manufacturers',
            'view-asset-requests', 'create-asset-requests', 'approve-asset-requests', 'reject-asset-requests',
        ]);

        // Management Role
        $management = Role::firstOrCreate(['name' => 'Management']);
        $management->syncPermissions([
            'view-assets', 'export-assets',
            'view-tickets', 'export-tickets',
            'view-users',
            'view-daily-activities',
            'view-reports', 'view-dashboard', 'view-kpi-dashboard',
            'view-asset-requests', 'approve-asset-requests', 'reject-asset-requests',
        ]);

        // User Role
        $user = Role::firstOrCreate(['name' => 'User']);
        $user->syncPermissions([
            'view-assets',
            'view-tickets', 'create-tickets', 'edit-tickets',
            'view-daily-activities', 'create-daily-activities', 'edit-daily-activities',
            'create-asset-requests',
        ]);
    }
}
