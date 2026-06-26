<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Staging table for imported institutions (Facility Report).
     *
     * The source CSV has no practitioner/customer, so these rows cannot go
     * straight into `otherapplications`. They sit here as PENDING until an
     * admin assigns the practitioner-in-charge, at which point a row is
     * pushed into `otherapplications` (otherservice_id = 3) as APPROVED.
     */
    public function up(): void
    {
        Schema::create('otherapplicationinstitutionimports', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->nullable();
            $table->integer('otherservice_id')->default(3); // Institution registration
            $table->string('tradename');                    // Institution Name
            $table->string('institution_type')->nullable();
            $table->string('institution_subtype')->nullable();
            $table->string('nature')->nullable();
            $table->string('institution_class')->nullable();
            $table->string('registration_no')->nullable();  // becomes certificate_number
            $table->string('registration_date')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            // Assigned later, when the practitioner-in-charge is known:
            $table->integer('customer_id')->nullable();
            $table->integer('customerprofession_id')->nullable();
            $table->integer('otherapplication_id')->nullable(); // set once pushed
            $table->string('period')->nullable();
            $table->string('status')->default('PENDING');
            $table->string('processed')->default('N');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('otherapplicationinstitutionimports');
    }
};
