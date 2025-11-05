<?php

use App\Models\Document;
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
        Schema::create('professiondocuments', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Registertype::class)->constrained();
            $table->foreignIdFor(Profession::class)->constrained();
            $table->foreignIdFor(Document::class)->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('professiondocuments');
    }
};
