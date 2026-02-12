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
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // Add role_code to users table if not exists
        if (!Schema::hasColumn('users', 'role_code')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('role_code', 50)->nullable()->after('password');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');

        if (Schema::hasColumn('users', 'role_code')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('role_code');
            });
        }
    }
};
