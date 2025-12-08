<?php

use App\Models\City;
use App\Models\Employmentlocation;
use App\Models\Employmentstatus;
use App\Models\Nationality;
use App\Models\Province;
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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('profile')->default('placeholder.jpg');
            $table->string('uuid')->nullable();
            $table->string('title')->nullable();
            $table->string('name')->nullable();
            $table->string('surname')->nullable();
            $table->string('previous_name')->nullable();
            $table->string('regnumber')->nullable();
            $table->string('identificationtype')->nullable();
            $table->string('identificationnumber')->nullable();
            $table->string('gender')->nullable();
            $table->string('maritalstatus')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('place_of_birth')->nullable();
            $table->string('dob')->nullable();
            $table->foreignIdFor(Nationality::class)->nullable();
            $table->foreignIdFor(Province::class)->nullable();
            $table->foreignIdFor(City::class)->nullable();
            $table->foreignIdFor(Employmentlocation::class)->nullable();
            $table->foreignIdFor(Employmentstatus::class)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
