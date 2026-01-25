<?php

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
        Schema::create('customerhistoricaldataprofessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customerhistoricaldata_id')
                ->constrained('customerhistoricaldata')
                ->onDelete('cascade')
                ->name('hist_data_prof_hist_data_id_foreign');
            $table->foreignId('profession_id')
                ->nullable()
                ->constrained('professions')
                ->name('hist_data_prof_profession_id_foreign');
            $table->string('registrationnumber')->nullable();
            $table->integer('registrationyear')->nullable();
            $table->string('practisingcertificatenumber')->nullable();
            $table->foreignId('registertype_id')
                ->nullable()
                ->constrained('registertypes')
                ->name('hist_data_prof_registertype_id_foreign');
            $table->integer('last_renewal_year')->nullable();
            $table->string('last_renewal_year_cdp_points')->nullable();
            $table->date('last_renewal_expire_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customerhistoricaldataprofessions');
    }
};
