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
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->enum('type', ['VIDEO', 'ARTICLE', 'ATTACHMENT']);
            $table->string('content_url')->nullable(); // For video URLs
            $table->text('content_text')->nullable(); // For article content
            $table->string('attachment_path')->nullable(); // For file attachments
            $table->integer('points')->default(0); // Points awarded for completion
            $table->integer('duration_minutes')->nullable(); // Estimated duration
            $table->enum('status', ['DRAFT', 'PUBLISHED', 'ARCHIVED'])->default('DRAFT');
            $table->integer('created_by'); // Admin who created it
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};