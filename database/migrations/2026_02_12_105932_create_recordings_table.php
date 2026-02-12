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
        Schema::create('recordings', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique()->comment('Kode unik recording');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('filename')->nullable()->comment('Nama file hasil recording');
            $table->string('file_path')->nullable()->comment('Path lengkap file');
            $table->string('custom_filename')->nullable()->comment('Custom filename dari form');
            $table->enum('status', ['recording', 'stopped', 'completed', 'failed'])->default('recording');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('stopped_at')->nullable();
            $table->integer('duration')->nullable()->comment('Durasi dalam detik');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recordings');
    }
};
