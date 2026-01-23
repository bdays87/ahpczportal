<?php

use App\Models\Nationality;
use App\Models\Profession;
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
        Schema::create('customerhistoricaldata', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained();
            $table->string('name')->nullable();
            $table->string('surname')->nullable();
            $table->string('gender')->nullable();
            $table->string('identificationnumber')->nullable();
            $table->date('dob')->nullable();
            $table->string('identificationtype')->nullable();
            $table->foreignIdFor(Nationality::class)->nullable();
            $table->string('address')->nullable();
            $table->string('placeofbirth')->nullable();
            $table->string('phone')->nullable();
            $table->foreignIdFor(Profession::class)->nullable();
            $table->string('registrationnumber')->nullable();
            $table->integer('registrationyear')->nullable();
            $table->string('practisingcertificatenumber')->nullable();
            $table->integer('applicationyear')->nullable();
            $table->foreignIdFor(Registertype::class)->nullable();
            $table->date('expiredate')->nullable();
            $table->string('status')->default('PENDING');
            $table->foreignIdFor(User::class, 'approvedby')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });

        Schema::create('customerhistoricaldatadocuments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customerhistoricaldata_id')
                ->constrained('customerhistoricaldata')
                ->onDelete('cascade')
                ->name('hist_data_docs_hist_data_id_foreign');
            $table->string('file');
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customerhistoricaldatadocuments');
        Schema::dropIfExists('customerhistoricaldata');
    }
};
