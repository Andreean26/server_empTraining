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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->constrained()->onDelete('set null');
            $table->uuid('uuid')->unique()->after('id');
            $table->boolean('is_active')->default(true)->after('email_verified_at');
            $table->json('preferences')->nullable()->after('is_active');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn(['role_id', 'uuid', 'is_active', 'preferences']);
            $table->dropSoftDeletes();
        });
    }
};
