<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengeluaran_bulanan', function (Blueprint $table) {
            $table->id();
            $table->date('bulan');
            $table->string('kategori', 100)->nullable();
            $table->text('keterangan');
            $table->decimal('jumlah', 15, 2);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('user_id')->on('users')->onDelete('set null');
            $table->timestamps();
            $table->index('bulan');
            $table->index('kategori');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengeluaran_bulanan');
    }
};