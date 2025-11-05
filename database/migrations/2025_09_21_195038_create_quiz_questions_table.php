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
        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained('activity_quizzes')->onDelete('cascade');
            $table->text('question');
            $table->enum('type', ['MULTIPLE_CHOICE', 'TRUE_FALSE', 'SINGLE_CHOICE'])->default('MULTIPLE_CHOICE');
            $table->integer('points')->default(1); // Points for correct answer
            $table->integer('order')->default(0); // Question order
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_questions');
    }
};