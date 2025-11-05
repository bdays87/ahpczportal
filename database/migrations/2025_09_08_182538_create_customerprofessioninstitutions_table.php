<?php

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
        Schema::create('customerprofessioninstitutions', function (Blueprint $table) {
            $table->id();
            $table->string("institution")->nullable(); 
            $table->string("qualification")->nullable();           
            $table->string("supervisor")->nullable();
            $table->string("supervisorphone")->nullable();
            $table->string("supervisoremail")->nullable();
            $table->string("is_attached")->default("NO");
            $table->string("organization")->nullable();
            $table->string("address")->nullable();
            $table->string("city")->nullable();
            $table->foreignIdFor(Customerprofession::class)->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customerprofessioninstitutions');
    }
};
