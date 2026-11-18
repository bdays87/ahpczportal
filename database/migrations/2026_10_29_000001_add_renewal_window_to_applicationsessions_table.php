<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applicationsessions', function (Blueprint $table) {
            $table->tinyInteger('renewal_window_start_month')->default(10)->after('year');
            $table->tinyInteger('renewal_window_end_month')->default(12)->after('renewal_window_start_month');
            $table->boolean('is_active')->default(false)->after('renewal_window_end_month');
        });
    }

    public function down(): void
    {
        Schema::table('applicationsessions', function (Blueprint $table) {
            $table->dropColumn(['renewal_window_start_month', 'renewal_window_end_month', 'is_active']);
        });
    }
};
