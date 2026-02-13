<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('departemen', function (Blueprint $table) {
            $table->id();
            $table->string('kode_departemen', 10)->unique();
            $table->string('nama_departemen', 100);
            $table->text('deskripsi')->nullable();
            $table->unsignedBigInteger('kepala_departemen')->nullable();
            $table->foreign('kepala_departemen')->references('user_id')->on('users')->onDelete('set null');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        
        DB::table('departemen')->insert([
            ['kode_departemen' => 'BT', 'nama_departemen' => 'Bebas Tanah', 'created_at' => now(), 'updated_at' => now()],
            ['kode_departemen' => 'TK', 'nama_departemen' => 'Teknik', 'created_at' => now(), 'updated_at' => now()],
            ['kode_departemen' => 'PR', 'nama_departemen' => 'Perijinan', 'created_at' => now(), 'updated_at' => now()],
            ['kode_departemen' => 'MK', 'nama_departemen' => 'Marketing', 'created_at' => now(), 'updated_at' => now()],
            ['kode_departemen' => 'KU', 'nama_departemen' => 'Keuangan', 'created_at' => now(), 'updated_at' => now()],
            ['kode_departemen' => 'UM', 'nama_departemen' => 'Umum', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('departemen');
    }
};