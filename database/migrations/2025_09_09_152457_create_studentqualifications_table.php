<?php

use App\Models\Customer;
use App\Models\Profession;
use App\Models\Qualificationcategory;
use App\Models\Qualificationlevel;
use App\Models\Student;
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
        Schema::create('studentqualifications', function (Blueprint $table) {
            $table->id();
            $table->integer("customerprofession_id");
            $table->foreignIdFor(Qualificationlevel::class);
            $table->string("institution");
            $table->string("qualification");
            $table->string("startyear");
            $table->string("endyear")->nullable();
            $table->string("grade")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('studentqualifications');
    }
};
