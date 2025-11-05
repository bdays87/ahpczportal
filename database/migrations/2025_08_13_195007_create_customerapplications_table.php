<?php

use App\Models\Customer;
use App\Models\Customerprofession;
use App\Models\Registertype;
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
        Schema::create('customerapplications', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Customer::class)->constrained();
            $table->foreignIdFor(Customerprofession::class)->constrained();
            $table->foreignIdFor(Registertype::class)->constrained();            
            $table->string('status')->default('PENDING');
            $table->string('certificate_number')->nullable();
            $table->date('certificate_expiry_date')->nullable();
            $table->integer('year')->nullable();
            $table->date('registration_date')->nullable();
            $table->foreignIdFor(User::class,'approvedby')->constrained()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customerapplications');
    }
};
