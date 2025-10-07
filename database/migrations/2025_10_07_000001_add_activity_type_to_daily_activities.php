<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('daily_activities', function (Blueprint $table) {
            $table->string('activity_type')->nullable()->after('description');
        });
    }

    public function down()
    {
        Schema::table('daily_activities', function (Blueprint $table) {
            $table->dropColumn('activity_type');
        });
    }
};