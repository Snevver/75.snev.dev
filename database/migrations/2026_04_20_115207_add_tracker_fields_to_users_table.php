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
        if (! Schema::hasColumn('users', 'username')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('username')->unique()->nullable()->after('name');
            });
        }

        if (! Schema::hasColumn('users', 'avatar')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('avatar')->nullable()->after('password');
            });
        }

        if (! Schema::hasColumn('users', 'is_admin')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('is_admin')->default(false)->after('avatar');
            });
        }

        if (! Schema::hasColumn('users', 'is_public')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('is_public')->default(false)->after('is_admin');
            });
        }

        if (! Schema::hasColumn('users', 'share_public_photos')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('share_public_photos')->default(false)->after('is_public');
            });
        }

        if (! Schema::hasColumn('users', 'timezone')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('timezone')->default('UTC')->after('share_public_photos');
            });
        }

        if (! Schema::hasColumn('users', 'challenge_start_date')) {
            Schema::table('users', function (Blueprint $table) {
                $table->date('challenge_start_date')->nullable()->after('timezone');
            });
        }

        if (! Schema::hasColumn('users', 'challenge_failed_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->timestamp('challenge_failed_at')->nullable()->after('challenge_start_date');
            });
        }

        if (! Schema::hasColumn('users', 'deactivated_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->timestamp('deactivated_at')->nullable()->after('challenge_failed_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $columns = [
            'username',
            'avatar',
            'is_admin',
            'is_public',
            'share_public_photos',
            'timezone',
            'challenge_start_date',
            'challenge_failed_at',
            'deactivated_at',
        ];

        $existingColumns = array_values(array_filter($columns, fn (string $column) => Schema::hasColumn('users', $column)));

        if ($existingColumns !== []) {
            Schema::table('users', function (Blueprint $table) use ($existingColumns) {
                $table->dropColumn($existingColumns);
            });
        }
    }
};
