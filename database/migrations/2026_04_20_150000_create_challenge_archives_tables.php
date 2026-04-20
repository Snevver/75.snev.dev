<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('challenge_archives', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->unsignedTinyInteger('days_logged')->default(0);
            $table->unsignedTinyInteger('perfect_days')->default(0);
            $table->unsignedTinyInteger('current_streak')->default(0);
            $table->unsignedTinyInteger('photos_uploaded')->default(0);
            $table->unsignedTinyInteger('days_remaining')->default(0);
            $table->timestamps();
        });

        Schema::create('challenge_archive_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('challenge_archive_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('day_number');
            $table->date('log_date');
            $table->string('status');
            $table->boolean('is_complete')->default(false);
            $table->text('notes')->nullable();
            $table->json('completed_tasks')->nullable();
            $table->string('photo_path')->nullable();
            $table->timestamps();

            $table->unique(['challenge_archive_id', 'day_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('challenge_archive_days');
        Schema::dropIfExists('challenge_archives');
    }
};
