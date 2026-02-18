<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_obs_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('obs_name', 100)->comment('Nama OBS, misal: OBS Ruang Meeting');
            $table->string('obs_url', 255)->comment('WebSocket URL, misal: ws://192.168.1.10:4455');
            $table->string('obs_password', 255)->nullable();
            $table->boolean('is_default')->default(0)->comment('Default OBS untuk user ini');
            $table->boolean('is_active')->default(1);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_obs_settings');
    }
};
