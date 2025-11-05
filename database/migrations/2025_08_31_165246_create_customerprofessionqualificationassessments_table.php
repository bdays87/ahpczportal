<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('customerprofessionqualificationassessments', function (Blueprint $table) {
            $table->id();
            $table->integer('customerprofession_id');
            $table->string('status')->default('PENDING');
            $table->foreignIdFor(User::class)->nullable();
            $table->text('comment')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customerprofessionqualificationassessments');
    }
};
