<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Fix foreign key constraints on assets table to have proper onDelete rules.
     * This ensures data integrity and prevents orphaned records.
     */
    public function up()
    {
        // Get existing foreign keys
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME
            FROM information_schema.TABLE_CONSTRAINTS
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = 'assets'
            AND CONSTRAINT_TYPE = 'FOREIGN KEY'
        ");
        
        $existingFKs = array_column($foreignKeys, 'CONSTRAINT_NAME');
        
        // Drop existing foreign keys
        Schema::table('assets', function (Blueprint $table) use ($existingFKs) {
            // Drop old FKs if they exist
            if (in_array('assets_model_id_foreign', $existingFKs)) {
                $table->dropForeign(['model_id']);
            }
            
            if (in_array('assets_division_id_foreign', $existingFKs)) {
                $table->dropForeign(['division_id']);
            }
            
            if (in_array('assets_supplier_id_foreign', $existingFKs)) {
                $table->dropForeign(['supplier_id']);
            }
            
            if (in_array('assets_assigned_to_foreign', $existingFKs)) {
                $table->dropForeign(['assigned_to']);
            }
            
            if (in_array('assets_purchase_order_id_foreign', $existingFKs)) {
                $table->dropForeign(['purchase_order_id']);
            }
            
            if (in_array('assets_warranty_type_id_foreign', $existingFKs)) {
                $table->dropForeign(['warranty_type_id']);
            }
            
            if (in_array('assets_invoice_id_foreign', $existingFKs)) {
                $table->dropForeign(['invoice_id']);
            }
        });

        // Add new FKs with proper onDelete rules
        Schema::table('assets', function (Blueprint $table) {
            // RESTRICT - Prevent deletion if assets exist
            $table->foreign('model_id')
                  ->references('id')
                  ->on('asset_models')
                  ->onDelete('restrict');
            
            $table->foreign('division_id')
                  ->references('id')
                  ->on('divisions')
                  ->onDelete('restrict');
            
            $table->foreign('supplier_id')
                  ->references('id')
                  ->on('suppliers')
                  ->onDelete('restrict');
            
            // If assigned_to exists, add FK with RESTRICT
            if (Schema::hasColumn('assets', 'assigned_to')) {
                $table->foreign('assigned_to')
                      ->references('id')
                      ->on('users')
                      ->onDelete('restrict');
            }
            
            // SET NULL - Allow cleanup of optional relations
            if (Schema::hasColumn('assets', 'purchase_order_id')) {
                $table->foreign('purchase_order_id')
                      ->references('id')
                      ->on('purchase_orders')
                      ->onDelete('set null');
            }
            
            if (Schema::hasColumn('assets', 'warranty_type_id')) {
                $table->foreign('warranty_type_id')
                      ->references('id')
                      ->on('warranty_types')
                      ->onDelete('restrict');
            }
            
            if (Schema::hasColumn('assets', 'invoice_id')) {
                $table->foreign('invoice_id')
                      ->references('id')
                      ->on('invoices')
                      ->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        // Drop the new FKs
        Schema::table('assets', function (Blueprint $table) {
            $table->dropForeign(['model_id']);
            $table->dropForeign(['division_id']);
            $table->dropForeign(['supplier_id']);
            
            if (Schema::hasColumn('assets', 'assigned_to')) {
                $table->dropForeign(['assigned_to']);
            }
            
            if (Schema::hasColumn('assets', 'purchase_order_id')) {
                $table->dropForeign(['purchase_order_id']);
            }
            
            if (Schema::hasColumn('assets', 'warranty_type_id')) {
                $table->dropForeign(['warranty_type_id']);
            }
            
            if (Schema::hasColumn('assets', 'invoice_id')) {
                $table->dropForeign(['invoice_id']);
            }
        });

        // Re-add old FKs without onDelete rules
        Schema::table('assets', function (Blueprint $table) {
            $table->foreign('model_id')->references('id')->on('asset_models');
            $table->foreign('division_id')->references('id')->on('divisions');
            $table->foreign('supplier_id')->references('id')->on('suppliers');
        });
    }
};
