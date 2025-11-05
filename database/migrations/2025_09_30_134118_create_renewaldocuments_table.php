<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Tire;
use App\Models\Currency;
use App\Models\Registertype;    
use App\Models\Document;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('renewaldocuments', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Tire::class)->constrained();
            $table->foreignIdFor(Registertype::class)->constrained();
            $table->foreignIdFor(Document::class)->constrained();
            $table->integer('applicationtype_id')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('renewaldocuments');
    }
};
