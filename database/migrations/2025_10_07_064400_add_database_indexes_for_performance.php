<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add indexes for frequently queried columns on users table
        Schema::table('users', function (Blueprint $table) {
            // Note: email already has unique index, division_id and employee_num don't exist in current schema
            // Only add indexes for columns that actually exist
            if (Schema::hasColumn('users', 'division_id')) {
                $table->index('division_id');
            }
            if (Schema::hasColumn('users', 'activated')) {
                $table->index(['activated', 'created_at']); // Composite index for active users
            }
        });

        // Add indexes for tickets table (check if they don't already exist)
        Schema::table('tickets', function (Blueprint $table) {
            // Skip basic foreign key indexes as they may already exist
            // Focus on composite indexes for performance
            $table->index(['ticket_status_id', 'created_at'], 'tickets_status_created_idx'); // For status-based queries with ordering
            $table->index(['user_id', 'ticket_status_id'], 'tickets_user_status_idx'); // For user's tickets filtered by status
            $table->index(['assigned_to', 'ticket_status_id'], 'tickets_assigned_status_idx'); // For admin's assigned tickets
            if (Schema::hasColumn('tickets', 'ticket_code')) {
                $table->index('ticket_code', 'tickets_code_idx'); // For ticket lookup
            }
        });

        // Add indexes for assets table (focus on composite indexes)
        Schema::table('assets', function (Blueprint $table) {
            // Skip basic foreign key indexes as they may already exist
            $table->index(['status_id', 'created_at'], 'assets_status_created_idx'); // For status-based queries with ordering
            if (Schema::hasColumn('assets', 'assigned_to')) {
                $table->index(['assigned_to', 'status_id'], 'assets_assigned_status_idx'); // For user's assets filtered by status
            }
        });

        // Add indexes for movements table (focus on composite indexes)
        Schema::table('movements', function (Blueprint $table) {
            // Skip basic foreign key indexes as they may already exist
            $table->index(['asset_id', 'created_at'], 'movements_asset_created_idx'); // For asset movement history
            $table->index(['created_at', 'asset_id'], 'movements_created_asset_idx'); // For recent movements
        });

        // Add indexes for tickets_entries table
        Schema::table('tickets_entries', function (Blueprint $table) {
            // Focus on composite indexes for performance
            $table->index(['ticket_id', 'created_at'], 'ticket_entries_ticket_created_idx'); // For ticket entries ordered by date
            if (Schema::hasColumn('tickets_entries', 'entry_type')) {
                $table->index(['entry_type', 'created_at'], 'ticket_entries_type_created_idx'); // For filtering by entry type
            }
        });

        // Add indexes for asset_requests table if exists
        if (Schema::hasTable('asset_requests')) {
            Schema::table('asset_requests', function (Blueprint $table) {
                $table->index('requested_by');
                $table->index('asset_type_id');
                $table->index('status');
                $table->index(['requested_by', 'status']); // For user's requests filtered by status
                $table->index(['status', 'created_at']); // For status-based queries with ordering
            });
        }

        // Add indexes for model_has_roles table (Spatie permissions)
        if (Schema::hasTable('model_has_roles')) {
            Schema::table('model_has_roles', function (Blueprint $table) {
                $table->index(['model_type', 'model_id']); // For role lookups
                $table->index('role_id'); // For reverse role lookups
            });
        }

        // Add indexes for daily_activities table if exists
        if (Schema::hasTable('daily_activities')) {
            Schema::table('daily_activities', function (Blueprint $table) {
                $table->index('user_id');
                $table->index('activity_date');
                $table->index('activity_type');
                $table->index(['user_id', 'activity_date']); // For user's daily activities
                $table->index(['activity_date', 'activity_type']); // For activity reports
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop indexes for users table
        Schema::table('users', function (Blueprint $table) {
            // Only drop indexes that were actually created
            if (Schema::hasColumn('users', 'division_id')) {
                $table->dropIndex(['division_id']);
            }
            if (Schema::hasColumn('users', 'activated')) {
                $table->dropIndex(['activated', 'created_at']);
            }
        });

        // Drop indexes for tickets table
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropIndex('tickets_status_created_idx');
            $table->dropIndex('tickets_user_status_idx');
            $table->dropIndex('tickets_assigned_status_idx');
            if (Schema::hasColumn('tickets', 'ticket_code')) {
                $table->dropIndex('tickets_code_idx');
            }
        });

        // Drop indexes for assets table
        Schema::table('assets', function (Blueprint $table) {
            $table->dropIndex('assets_status_created_idx');
            if (Schema::hasColumn('assets', 'assigned_to')) {
                $table->dropIndex('assets_assigned_status_idx');
            }
        });

        // Drop indexes for movements table
        Schema::table('movements', function (Blueprint $table) {
            $table->dropIndex('movements_asset_created_idx');
            $table->dropIndex('movements_created_asset_idx');
        });

        // Drop indexes for tickets_entries table
        Schema::table('tickets_entries', function (Blueprint $table) {
            $table->dropIndex('ticket_entries_ticket_created_idx');
            if (Schema::hasColumn('tickets_entries', 'entry_type')) {
                $table->dropIndex('ticket_entries_type_created_idx');
            }
        });

        // Drop indexes for asset_requests table if exists
        if (Schema::hasTable('asset_requests')) {
            Schema::table('asset_requests', function (Blueprint $table) {
                $table->dropIndex(['user_id']);
                $table->dropIndex(['asset_id']);
                $table->dropIndex(['status']);
                $table->dropIndex(['user_id', 'status']);
                $table->dropIndex(['status', 'created_at']);
            });
        }

        // Drop indexes for model_has_roles table (Spatie permissions)
        if (Schema::hasTable('model_has_roles')) {
            Schema::table('model_has_roles', function (Blueprint $table) {
                $table->dropIndex(['model_type', 'model_id']);
                $table->dropIndex(['role_id']);
            });
        }

        // Drop indexes for daily_activities table if exists
        if (Schema::hasTable('daily_activities')) {
            Schema::table('daily_activities', function (Blueprint $table) {
                $table->dropIndex(['user_id']);
                $table->dropIndex(['activity_date']);
                $table->dropIndex(['activity_type']);
                $table->dropIndex(['user_id', 'activity_date']);
                $table->dropIndex(['activity_date', 'activity_type']);
            });
        }
    }
};
