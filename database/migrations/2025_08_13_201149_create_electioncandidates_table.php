<?php

use App\Models\Customer;
use App\Models\Electionposition;
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
        Schema::create('electioncandidates', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Electionposition::class)->constrained();
            $table->foreignIdFor(Customer::class)->constrained();
            $table->string('profile_picture')->nullable();
            $table->string('description');
            $table->string('status')->default('PENDING');
            $table->foreignIdFor(User::class,'createdby')->constrained()->nullable();
            $table->foreignIdFor(User::class,'updatedby')->constrained()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('electioncandidates');
    }
};
