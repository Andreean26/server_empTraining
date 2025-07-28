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
        Schema::create('trainings', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('title');
            $table->text('description');
            $table->string('trainer_name');
            $table->string('trainer_email')->nullable();
            $table->date('training_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('location')->nullable();
            $table->integer('max_participants')->default(0);
            $table->string('pdf_material')->nullable(); // Path to PDF file
            $table->boolean('is_active')->default(true);
            $table->json('additional_info')->nullable(); // Flexible data storage
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trainings');
    }
};
