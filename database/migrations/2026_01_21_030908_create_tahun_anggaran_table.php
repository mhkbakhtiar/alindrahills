<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tahun_anggaran', function (Blueprint $table) {
            $table->id();
            $table->year('tahun')->unique();
            $table->date('periode_awal');
            $table->date('periode_akhir');
            $table->enum('status', ['aktif', 'tutup_buku'])->default('aktif');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tahun_anggaran');
    }
};