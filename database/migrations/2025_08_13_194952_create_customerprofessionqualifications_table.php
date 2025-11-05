<?php

use App\Models\Customerprofession;
use App\Models\Qualificationcategory;
use App\Models\Qualificationlevel;
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
            $table->foreignIdFor(Customerprofession::class,'customerprofessionid')->constrained();
            $table->foreignIdFor(Qualificationcategory::class,'qualificationcategoryid')->constrained();
            $table->foreignIdFor(Qualificationlevel::class,'qualificationlevelid')->constrained();
            $table->string('file')->nullable();
            $table->string('status')->default('PENDING');
            $table->foreignIdFor(User::class,'verifiedby')->constrained()->nullable();
            $table->foreignIdFor(User::class,'approvedby')->constrained()->nullable();
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
