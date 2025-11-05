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
        Schema::table('mycdps', function (Blueprint $table) {
            $table->text('assessment_notes')->nullable()->after('comment');
            $table->timestamp('assessed_at')->nullable()->after('assessed_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mycdps', function (Blueprint $table) {
            $table->dropColumn(['assessment_notes', 'assessed_at']);
        });
    }
};