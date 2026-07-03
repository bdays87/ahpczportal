<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A service/test can now have sub-tests (self-referencing parent_id).
     */
    public function up(): void
    {
        Schema::table('otherapplicationinstservices', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_id')->nullable()->after('otherapplication_id');
        });
    }

    public function down(): void
    {
        Schema::table('otherapplicationinstservices', function (Blueprint $table) {
            $table->dropColumn('parent_id');
        });
    }
};
