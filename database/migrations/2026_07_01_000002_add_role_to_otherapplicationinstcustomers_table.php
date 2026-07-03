<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Employees now have a role within the institution:
     * IN_CHARGE, RESIDENT_SCIENTIST or EMPLOYEE.
     */
    public function up(): void
    {
        Schema::table('otherapplicationinstcustomers', function (Blueprint $table) {
            $table->string('role')->default('EMPLOYEE')->after('customer_id');
        });
    }

    public function down(): void
    {
        Schema::table('otherapplicationinstcustomers', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
