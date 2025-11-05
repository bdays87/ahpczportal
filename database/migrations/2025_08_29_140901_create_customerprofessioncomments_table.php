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
        Schema::create('customerprofessioncomments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customerprofession_id')->constrained('customerprofessions');
            $table->foreignId('user_id')->constrained('users');
            $table->string('commenttype')->default('comment');
            $table->text('comment');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customerprofessioncomments');
    }
};
