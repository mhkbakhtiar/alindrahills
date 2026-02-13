<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_jurnal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_jurnal')->constrained('jurnal')->onDelete('cascade');
            $table->string('kode_perkiraan', 10);
            $table->foreign('kode_perkiraan')->references('kode_perkiraan')->on('perkiraan')->onDelete('restrict');
            $table->string('kode_kavling', 10)->nullable();
            $table->foreign('kode_kavling')->references('kavling')->on('project_locations')->onDelete('set null');
            $table->unsignedBigInteger('id_user')->nullable();
            $table->foreign('id_user')->references('user_id')->on('users')->onDelete('set null');
            $table->text('keterangan')->nullable();
            $table->decimal('debet', 20, 2)->default(0);
            $table->decimal('kredit', 20, 2)->default(0);
            $table->integer('urutan')->default(0);
            $table->timestamps();
            $table->index('id_jurnal');
            $table->index('kode_perkiraan');
            $table->index('kode_kavling');
            $table->index('id_user');
            $table->index(['kode_perkiraan', 'kode_kavling']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_jurnal');
    }
};