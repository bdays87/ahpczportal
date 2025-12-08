<?php

use App\Models\Customer;
use App\Models\Customertype;
use App\Models\Employmentlocation;
use App\Models\Employmentstatus;
use App\Models\Profession;
use App\Models\Registertype;
use App\Models\Tire;
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
        Schema::create('customerprofessions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Customer::class)->constrained();
            $table->foreignIdFor(Profession::class)->constrained();
            $table->foreignIdFor(Customertype::class)->constrained();
            $table->foreignIdFor(Employmentstatus::class)->constrained();
            $table->foreignIdFor(Employmentlocation::class)->constrained();
            $table->foreignIdFor(Registertype::class)->nullable();
            $table->foreignIdFor(Tire::class)->nullable();
            $table->string('uuid')->nullable();
            $table->string('employmentsector');
            $table->string("status")->default("PENDING");           
            $table->integer('year');
            $table->integer('created_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customerprofessions');
    }
};
