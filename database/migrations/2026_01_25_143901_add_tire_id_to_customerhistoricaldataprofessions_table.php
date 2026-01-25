<?php

use App\Models\Tire;
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
        Schema::table('customerhistoricaldataprofessions', function (Blueprint $table) {
            $table->foreignIdFor(Tire::class)
                ->nullable()
                ->after('registertype_id')
                ->constrained('tires')
                ->name('hist_data_prof_tire_id_foreign');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customerhistoricaldataprofessions', function (Blueprint $table) {
            $table->dropForeign('hist_data_prof_tire_id_foreign');
            $table->dropColumn('tire_id');
        });
    }
};
