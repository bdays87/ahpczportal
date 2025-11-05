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
            $table->string('name');
            $table->string('surname');
            $table->string('previous_name')->nullable();
            $table->string('regnumber')->nullable();
            $table->string('identificationtype');
            $table->string('identificationnumber');
            $table->string('gender');
            $table->string('maritalstatus');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('address');
            $table->string('place_of_birth');
            $table->date('dob');
            $table->foreignIdFor(Nationality::class)->constrained();
            $table->foreignIdFor(Province::class)->constrained();
            $table->foreignIdFor(City::class)->constrained();
            $table->foreignIdFor(Employmentlocation::class)->constrained();
            $table->foreignIdFor(Employmentstatus::class)->constrained();
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
