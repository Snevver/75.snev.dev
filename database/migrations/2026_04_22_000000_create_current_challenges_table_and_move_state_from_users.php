<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('current_challenges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->unique();
            $table->date('start_date')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->timestamps();
        });

        DB::table('users')
            ->select(['id', 'challenge_start_date', 'challenge_failed_at'])
            ->orderBy('id')
            ->chunkById(100, function ($users): void {
                foreach ($users as $user) {
                    if (! $user->challenge_start_date && ! $user->challenge_failed_at) {
                        continue;
                    }

                    DB::table('current_challenges')->insert([
                        'user_id' => $user->id,
                        'start_date' => $user->challenge_start_date,
                        'failed_at' => $user->challenge_failed_at,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }, 'id');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['challenge_start_date', 'challenge_failed_at']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->date('challenge_start_date')->nullable()->after('timezone');
            $table->timestamp('challenge_failed_at')->nullable()->after('challenge_start_date');
        });

        $challenges = DB::table('current_challenges')->get();

        foreach ($challenges as $challenge) {
            DB::table('users')
                ->where('id', $challenge->user_id)
                ->update([
                    'challenge_start_date' => $challenge->start_date,
                    'challenge_failed_at' => $challenge->failed_at,
                ]);
        }

        Schema::dropIfExists('current_challenges');
    }
};