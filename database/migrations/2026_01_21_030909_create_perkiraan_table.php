<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('perkiraan', function (Blueprint $table) {
            $table->id();
            $table->string('kode_perkiraan', 10)->unique();
            $table->string('nama_perkiraan', 255);
            $table->decimal('saldo_debet', 20, 2)->default(0);
            $table->decimal('saldo_kredit', 20, 2)->default(0);
            $table->decimal('anggaran', 20, 2)->nullable();
            $table->enum('jenis_akun', ['Aset', 'Kewajiban', 'Modal', 'Pendapatan', 'Biaya']);
            $table->enum('kategori', ['Lancar', 'Tetap', 'Jangka Panjang', 'Persediaan', 'Operasional', 'Non Operasional'])->nullable();
            $table->string('departemen', 50)->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('perkiraan')->onDelete('set null');
            $table->integer('level')->default(1);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_header')->default(false);
            $table->boolean('is_cash_bank')->default(false);
            $table->boolean('is_hpp')->default(false);
            $table->text('keterangan')->nullable();
            $table->date('tanggal')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('user_id')->on('users')->onDelete('set null');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('updated_by')->references('user_id')->on('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
            $table->index('kode_perkiraan');
            $table->index('jenis_akun');
            $table->index('departemen');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perkiraan');
    }
};