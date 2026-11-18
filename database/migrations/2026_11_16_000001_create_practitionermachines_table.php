<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * History of machines/equipment a practitioner has used.
     * Rows are never removed when a machine goes out of use: date_to / status
     * record the period so we always know who used which machine and when.
     */
    public function up(): void
    {
        Schema::create('practitionermachines', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->nullable();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('customerprofession_id')->nullable();
            $table->unsignedBigInteger('otherapplication_id')->nullable(); // institution where the machine was used
            $table->string('name');
            $table->string('make')->nullable();
            $table->string('model')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('asset_tag')->nullable();
            $table->string('location')->nullable();
            $table->date('date_from')->nullable();
            $table->date('date_to')->nullable();
            $table->string('status')->default('IN_USE'); // IN_USE, NOT_IN_USE
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->index('customer_id');
            $table->index('serial_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('practitionermachines');
    }
};
