<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_audit_trails', function (Blueprint $table) {
            $table->id();
            $table->string('module');           // JOURNAL, AP, AR, COA, BUDGET, etc.
            $table->string('action');           // CREATE, UPDATE, DELETE, POST, APPROVE, REVERSE, CLOSE
            $table->string('record_type');      // model class name
            $table->unsignedBigInteger('record_id');
            $table->string('reference')->nullable();
            $table->text('old_values')->nullable(); // JSON
            $table->text('new_values')->nullable(); // JSON
            $table->string('ip_address')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_audit_trails');
    }
};
