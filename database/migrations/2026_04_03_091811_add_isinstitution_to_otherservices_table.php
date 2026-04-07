<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('otherservices', function (Blueprint $table) {
            $table->string('isinstitution')->default('N')->after('requiretradename');
        });
    }

    public function down(): void
    {
        Schema::table('otherservices', function (Blueprint $table) {
            $table->dropColumn('isinstitution');
        });
    }
};
