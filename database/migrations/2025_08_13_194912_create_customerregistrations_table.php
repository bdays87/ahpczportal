<?php

use App\Models\Customer;
use App\Models\Customerprofession;
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
        Schema::create('customerregistrations', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Customer::class)->constrained();
            $table->foreignIdFor(Customerprofession::class)->constrained();
            $table->string('status')->default('PENDING');
            $table->string('certificatenumber')->nullable();
            $table->date('certificateexpirydate')->nullable();
            $table->integer('year')->nullable();
            $table->date('registrationdate')->nullable();            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customerregistrations');
    }
};
