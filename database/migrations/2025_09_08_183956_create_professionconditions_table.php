<?php

use App\Models\Customertype;
use App\Models\Profession;
use App\Models\Registertype;
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
        Schema::create('professionconditions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Profession::class)->constrained();
            $table->foreignIdFor(Customertype::class)->constrained();
            $table->string("condition")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('professionconditions');
    }
};
