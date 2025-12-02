<?php

use App\Models\Customerprofession;
use App\Models\Qualificationcategory;
use App\Models\Qualificationlevel;
use App\Models\Qualification;
use App\Models\User;
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
        Schema::create('customerprofessionqualifications', function (Blueprint $table) {
            $table->id();
            $table->integer('customerprofession_id');
            $table->integer('qualificationcategory_id');
            $table->integer('qualificationlevel_id');
            $table->integer('qualification_id');
            $table->string('year');
            $table->string('file')->nullable();
            $table->string('status')->default('PENDING');
            $table->foreignIdFor(User::class,'verifiedby')->nullable();
            $table->foreignIdFor(User::class,'approvedby')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customerprofessionqualifications');
    }
};
