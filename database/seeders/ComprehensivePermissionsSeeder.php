<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ComprehensivePermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * This seeder creates all permissions required by the application
     * and assigns them to appropriate roles.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $guardName = config('auth.defaults.guard', 'web');

        // Define all permissions with their display names and descriptions
        $permissions = [
            // Asset Permissions
            ['name' => 'view-assets', 'display_name' => 'View Assets', 'description' => 'View asset list and details'],
            ['name' => 'create-assets', 'display_name' => 'Create Assets', 'description' => 'Create new assets'],
            ['name' => 'edit-assets', 'display_name' => 'Edit Assets', 'description' => 'Edit existing assets'],
            ['name' => 'delete-assets', 'display_name' => 'Delete Assets', 'description' => 'Delete assets'],
            ['name' => 'export-assets', 'display_name' => 'Export Assets', 'description' => 'Export assets to Excel/CSV'],
            ['name' => 'import-assets', 'display_name' => 'Import Assets', 'description' => 'Import assets from Excel/CSV'],
            
            // Ticket Permissions
            ['name' => 'view-tickets', 'display_name' => 'View Tickets', 'description' => 'View ticket list and details'],
            ['name' => 'create-tickets', 'display_name' => 'Create Tickets', 'description' => 'Create new tickets'],
            ['name' => 'edit-tickets', 'display_name' => 'Edit Tickets', 'description' => 'Edit existing tickets'],
            ['name' => 'delete-tickets', 'display_name' => 'Delete Tickets', 'description' => 'Delete tickets'],
            ['name' => 'assign-tickets', 'display_name' => 'Assign Tickets', 'description' => 'Assign tickets to users'],
            ['name' => 'export-tickets', 'display_name' => 'Export Tickets', 'description' => 'Export tickets to Excel/CSV'],
            
            // Daily Activity Permissions
            ['name' => 'view-daily-activities', 'display_name' => 'View Daily Activities', 'description' => 'View daily activities'],
            ['name' => 'create-daily-activities', 'display_name' => 'Create Daily Activities', 'description' => 'Create new daily activities'],
            ['name' => 'edit-daily-activities', 'display_name' => 'Edit Daily Activities', 'description' => 'Edit daily activities'],
            ['name' => 'delete-daily-activities', 'display_name' => 'Delete Daily Activities', 'description' => 'Delete daily activities'],
            
            // Dashboard & Report Permissions
            ['name' => 'view-kpi-dashboard', 'display_name' => 'View KPI Dashboard', 'description' => 'View KPI dashboard'],
            ['name' => 'view-reports', 'display_name' => 'View Reports', 'description' => 'View reports section'],
            ['name' => 'view-management-dashboard', 'display_name' => 'View Management Dashboard', 'description' => 'View management dashboard'],
            
            // Model/Configuration Permissions
            ['name' => 'view-models', 'display_name' => 'View Models', 'description' => 'View asset models and specifications'],
            ['name' => 'create-models', 'display_name' => 'Create Models', 'description' => 'Create asset models'],
            ['name' => 'edit-models', 'display_name' => 'Edit Models', 'description' => 'Edit asset models'],
            ['name' => 'delete-models', 'display_name' => 'Delete Models', 'description' => 'Delete asset models'],
            
            // Supplier Permissions
            ['name' => 'view-suppliers', 'display_name' => 'View Suppliers', 'description' => 'View supplier list'],
            ['name' => 'create-suppliers', 'display_name' => 'Create Suppliers', 'description' => 'Create new suppliers'],
            ['name' => 'edit-suppliers', 'display_name' => 'Edit Suppliers', 'description' => 'Edit suppliers'],
            ['name' => 'delete-suppliers', 'display_name' => 'Delete Suppliers', 'description' => 'Delete suppliers'],
            
            // Location Permissions
            ['name' => 'view-locations', 'display_name' => 'View Locations', 'description' => 'View location list'],
            ['name' => 'create-locations', 'display_name' => 'Create Locations', 'description' => 'Create new locations'],
            ['name' => 'edit-locations', 'display_name' => 'Edit Locations', 'description' => 'Edit locations'],
            ['name' => 'delete-locations', 'display_name' => 'Delete Locations', 'description' => 'Delete locations'],
            
            // Division Permissions
            ['name' => 'view-divisions', 'display_name' => 'View Divisions', 'description' => 'View division list'],
            ['name' => 'create-divisions', 'display_name' => 'Create Divisions', 'description' => 'Create new divisions'],
            ['name' => 'edit-divisions', 'display_name' => 'Edit Divisions', 'description' => 'Edit divisions'],
            ['name' => 'delete-divisions', 'display_name' => 'Delete Divisions', 'description' => 'Delete divisions'],
            
            // Invoice & Budget Permissions
            ['name' => 'view-invoices', 'display_name' => 'View Invoices', 'description' => 'View invoices and budgets'],
            ['name' => 'create-invoices', 'display_name' => 'Create Invoices', 'description' => 'Create new invoices'],
            ['name' => 'edit-invoices', 'display_name' => 'Edit Invoices', 'description' => 'Edit invoices'],
            ['name' => 'delete-invoices', 'display_name' => 'Delete Invoices', 'description' => 'Delete invoices'],
            
            // Import/Export Permissions
            ['name' => 'export-data', 'display_name' => 'Export Data', 'description' => 'Export data to Excel/CSV'],
            ['name' => 'import-data', 'display_name' => 'Import Data', 'description' => 'Import data from Excel/CSV'],
            
            // User Management Permissions
            ['name' => 'view-users', 'display_name' => 'View Users', 'description' => 'View user list'],
            ['name' => 'create-users', 'display_name' => 'Create Users', 'description' => 'Create new users'],
            ['name' => 'edit-users', 'display_name' => 'Edit Users', 'description' => 'Edit existing users'],
            ['name' => 'delete-users', 'display_name' => 'Delete Users', 'description' => 'Delete users'],
            ['name' => 'change-role', 'display_name' => 'Change User Role', 'description' => 'Change user roles'],
        ];

        // Create all permissions
        echo "Creating permissions...\n";
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name'], 'guard_name' => $guardName],
                [
                    'display_name' => $permission['display_name'],
                    'description' => $permission['description']
                ]
            );
            echo "  ✓ {$permission['name']}\n";
        }

        // Get roles
        $superAdminRole = Role::where('name', 'super-admin')->first();
        $adminRole = Role::where('name', 'admin')->first();
        $managementRole = Role::where('name', 'management')->first();
        $userRole = Role::where('name', 'user')->first();

        if (!$superAdminRole || !$adminRole || !$managementRole || !$userRole) {
            echo "\n⚠️  ERROR: Roles not found! Run RolesTableSeeder first.\n";
            return;
        }

        // Assign permissions to roles
        echo "\nAssigning permissions to roles...\n";

        // Super Admin - ALL PERMISSIONS
        $superAdminRole->syncPermissions(Permission::all());
        echo "  ✓ Super Admin: " . Permission::count() . " permissions\n";

        // Admin - Most permissions except super-admin specific ones
        $adminPermissions = [
            'view-assets', 'create-assets', 'edit-assets', 'delete-assets', 'export-assets', 'import-assets',
            'view-tickets', 'create-tickets', 'edit-tickets', 'delete-tickets', 'assign-tickets', 'export-tickets',
            'view-daily-activities', 'create-daily-activities', 'edit-daily-activities', 'delete-daily-activities',
            'view-kpi-dashboard', 'view-reports',
            'export-data', 'import-data',
            'view-users', 'create-users', 'edit-users',
        ];
        $adminRole->syncPermissions($adminPermissions);
        echo "  ✓ Admin: " . count($adminPermissions) . " permissions\n";

        // Management - View and report permissions
        $managementPermissions = [
            'view-assets',
            'view-tickets', 'create-tickets', 'edit-tickets',
            'view-daily-activities', 'create-daily-activities', 'edit-daily-activities',
            'view-kpi-dashboard', 'view-reports', 'view-management-dashboard',
        ];
        $managementRole->syncPermissions($managementPermissions);
        echo "  ✓ Management: " . count($managementPermissions) . " permissions\n";

        // User - Limited permissions
        $userPermissions = [
            'view-tickets', 'create-tickets',
        ];
        $userRole->syncPermissions($userPermissions);
        echo "  ✓ User: " . count($userPermissions) . " permissions\n";

        echo "\n✅ All permissions created and assigned successfully!\n";
    }
}
