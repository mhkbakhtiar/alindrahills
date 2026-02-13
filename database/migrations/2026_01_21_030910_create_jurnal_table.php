<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jurnal', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_bukti', 50)->unique();
            $table->date('tanggal');
            $table->text('keterangan')->nullable();
            $table->enum('jenis_jurnal', ['umum', 'penyesuaian', 'penutup', 'pembalik'])->default('umum');
            $table->string('departemen', 50)->nullable();
            $table->foreignId('id_tahun_anggaran')->nullable()->constrained('tahun_anggaran')->onDelete('set null');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('user_id')->on('users')->onDelete('set null');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('updated_by')->references('user_id')->on('users')->onDelete('set null');
            $table->enum('status', ['draft', 'posted', 'void'])->default('posted');
            $table->timestamps();
            $table->softDeletes();
            $table->index('nomor_bukti');
            $table->index('tanggal');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jurnal');
    }
};