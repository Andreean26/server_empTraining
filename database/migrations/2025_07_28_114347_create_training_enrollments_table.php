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
        Schema::create('training_enrollments', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('training_id')->constrained()->onDelete('cascade');
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['enrolled', 'attended', 'completed', 'cancelled'])->default('enrolled');
            $table->datetime('enrolled_at');
            $table->datetime('attended_at')->nullable();
            $table->datetime('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_certified')->default(false);
            $table->json('evaluation_data')->nullable(); // Store evaluation scores, feedback, etc.
            $table->timestamps();
            $table->softDeletes();
            
            // Unique constraint to prevent duplicate enrollments
            $table->unique(['training_id', 'employee_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_enrollments');
    }
};
