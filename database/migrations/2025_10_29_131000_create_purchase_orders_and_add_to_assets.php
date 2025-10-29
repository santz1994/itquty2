<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('purchase_orders')) {
            Schema::create('purchase_orders', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('po_number')->unique();
                // suppliers.id is an unsigned integer (increments), so match the column type
                $table->unsignedInteger('supplier_id')->nullable()->index();
                $table->date('order_date')->nullable();
                $table->decimal('total', 15, 2)->nullable();
                $table->string('status')->default('draft');
                $table->timestamps();

                $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('set null');
            });
        }

        // Add nullable purchase_order_id to assets
        Schema::table('assets', function (Blueprint $table) {
            if (!Schema::hasColumn('assets', 'purchase_order_id')) {
                $table->unsignedBigInteger('purchase_order_id')->nullable()->after('invoice_id')->index();
                $table->foreign('purchase_order_id')->references('id')->on('purchase_orders')->onDelete('set null');
            }
        });
    }

    public function down()
    {
        Schema::table('assets', function (Blueprint $table) {
            if (Schema::hasColumn('assets', 'purchase_order_id')) {
                $table->dropForeign(['purchase_order_id']);
                $table->dropColumn('purchase_order_id');
            }
        });

        Schema::dropIfExists('purchase_orders');
    }
};
