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
        Schema::create('customer_quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->constrained('activity_enrollments')->onDelete('cascade');
            $table->foreignId('quiz_id')->constrained('activity_quizzes')->onDelete('cascade');
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->integer('attempt_number')->default(1);
            $table->json('answers'); // Store customer's answers
            $table->integer('score')->default(0); // Points scored
            $table->integer('total_questions');
            $table->integer('correct_answers')->default(0);
            $table->decimal('percentage', 5, 2)->default(0.00);
            $table->boolean('passed')->default(false);
            $table->integer('time_taken_minutes')->nullable();
            $table->timestamp('started_at');
            $table->timestamp('completed_at')->nullable();
            $table->enum('status', ['IN_PROGRESS', 'COMPLETED', 'ABANDONED'])->default('IN_PROGRESS');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_quiz_attempts');
    }
};