<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('user_id');

            $table->string('username', 50)->unique();
            $table->string('password', 255);
            $table->string('full_name', 100);
            $table->enum('role', ['superadmin', 'admin', 'teknik', 'owner']);

            $table->string('email', 100)->nullable();
            $table->string('phone', 20)->nullable();

            $table->boolean('is_active')->default(1);

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')
                  ->useCurrent()
                  ->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
