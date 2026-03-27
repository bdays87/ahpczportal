<?php

use App\Models\Institution;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('institution_services', function (Blueprint $table) {
            $table->foreignIdFor(Institution::class)->after('id')->constrained();
        });
    }

    public function down(): void
    {
        Schema::table('institution_services', function (Blueprint $table) {
            $table->dropForeignIdFor(Institution::class);
        });
    }
};
