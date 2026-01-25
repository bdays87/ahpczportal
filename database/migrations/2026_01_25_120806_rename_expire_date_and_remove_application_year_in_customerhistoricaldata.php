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
        Schema::table('customerhistoricaldata', function (Blueprint $table) {
            $table->date('last_renewal_expire_date')->nullable()->after('last_renewal_year_cdp_points');
            $table->dropColumn(['expiredate', 'applicationyear']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customerhistoricaldata', function (Blueprint $table) {
            $table->date('expiredate')->nullable()->after('registertype_id');
            $table->integer('applicationyear')->nullable()->after('practisingcertificatenumber');
            $table->dropColumn('last_renewal_expire_date');
        });
    }
};
