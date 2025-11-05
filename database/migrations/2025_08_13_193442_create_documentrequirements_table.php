<?php

use App\Models\Document;
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
        Schema::create('documentrequirements', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Document::class)->constrained();
            $table->foreignIdFor(Tire::class)->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documentrequirements');
    }
};
