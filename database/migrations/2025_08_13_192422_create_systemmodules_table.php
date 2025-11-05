<?php

use App\Models\Accounttype;
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
        Schema::create('systemmodules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignIdFor(Accounttype::class)->constrained();
            $table->string('icon')->nullable();
            $table->string('default_permission');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('systemmodules');
    }
};
