<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('project_locations', function (Blueprint $table) {
            if (!Schema::hasColumn('project_locations', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->comment('Pemilik Kavling');
                $table->foreign('user_id')->references('user_id')->on('users')->onDelete('set null');
            }
            
            if (!Schema::hasColumn('project_locations', 'luas_tanah')) {
                $table->decimal('luas_tanah', 10, 2)->nullable();
            }
            
            if (!Schema::hasColumn('project_locations', 'luas_bangunan')) {
                $table->decimal('luas_bangunan', 10, 2)->nullable();
            }
            
            if (!Schema::hasColumn('project_locations', 'tipe_rumah')) {
                $table->string('tipe_rumah', 50)->nullable();
            }
            
            if (!Schema::hasColumn('project_locations', 'status_kavling')) {
                $table->enum('status_kavling', ['tersedia', 'booking', 'dp', 'cicilan', 'lunas', 'proses_bangun', 'serah_terima'])->default('tersedia');
            }
            
            if (!Schema::hasColumn('project_locations', 'harga_jual')) {
                $table->decimal('harga_jual', 15, 2)->nullable();
            }
            
            if (!Schema::hasColumn('project_locations', 'total_dibayar')) {
                $table->decimal('total_dibayar', 15, 2)->default(0);
            }
            
            if (!Schema::hasColumn('project_locations', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    public function down(): void
    {
        Schema::table('project_locations', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'luas_tanah', 'luas_bangunan', 'tipe_rumah', 'status_kavling', 'harga_jual', 'total_dibayar', 'deleted_at']);
        });
    }
};