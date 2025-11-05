<?php

use App\Models\Customer;
use App\Models\Election;
use App\Models\Electioncandidate;
use App\Models\Electionposition;
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
        Schema::create('electionvotes', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Election::class)->constrained();
            $table->foreignIdFor(Customer::class)->constrained();
            $table->foreignIdFor(Electionposition::class)->constrained();
            $table->foreignIdFor(Electioncandidate::class)->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('electionvotes');
    }
};
