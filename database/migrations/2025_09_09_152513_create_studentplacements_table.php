<?php

use App\Models\Customer;
use App\Models\Customerprofession;
use App\Models\Student;
use App\Models\Studentqualification;
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
        Schema::create('studentplacements', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Customerprofession::class);
            $table->string("company");
            $table->string("position"); 
            $table->string("startdate");
            $table->string("enddate")->nullable();
            $table->string("supervisorname");
            $table->string("supervisorphone");
            $table->string("supervisoremail");
            $table->string("supervisorposition"); 
            $table->string("is_supervisor_registered")->enum("YES","NO");
            $table->string("regnumber")->nullable();
            $table->integer('customer_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('studentplacements');
    }
};
