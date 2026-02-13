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
        Schema::table('payroll_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('activity_id')->nullable()->after('period_end');
            
            $table->foreign('activity_id')
                  ->references('activity_id')
                  ->on('activities')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payroll_requests', function (Blueprint $table) {
            $table->dropForeign(['activity_id']);
            $table->dropColumn('activity_id');
        });
    }
};