<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cicilan_kavling', function (Blueprint $table) {
            $table->id();
            $table->string('kode_kavling', 10);
            $table->foreign('kode_kavling')->references('kavling')->on('project_locations')->onDelete('cascade');
            $table->unsignedBigInteger('id_user');
            $table->foreign('id_user')->references('user_id')->on('users')->onDelete('cascade');
            $table->integer('nomor_cicilan');
            $table->date('tanggal_jatuh_tempo');
            $table->decimal('jumlah', 15, 2);
            $table->enum('status', ['belum_bayar', 'sudah_bayar', 'telat'])->default('belum_bayar');
            $table->date('tanggal_bayar')->nullable();
            $table->foreignId('id_jurnal_pembayaran')->nullable()->constrained('jurnal')->onDelete('set null');
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->index(['kode_kavling', 'nomor_cicilan']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cicilan_kavling');
    }
};