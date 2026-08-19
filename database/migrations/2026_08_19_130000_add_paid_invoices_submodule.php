<?php

use App\Models\Systemmodule;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Adds "Paid Invoices" under the Finance menu section, pointing at the
     * paidinvoices.index route. Reuses the existing invoices.access
     * permission (same as the "Invoices" submodule) so it's immediately
     * visible to whoever can already see Invoices — no new permission/role
     * setup required.
     */
    public function up(): void
    {
        if (DB::table('submodules')->where('url', 'paidinvoices.index')->exists()) {
            return;
        }

        $finance = Systemmodule::where('name', 'Finance')->first();

        if (! $finance) {
            return;
        }

        DB::table('submodules')->insert([
            'systemmodule_id' => $finance->id,
            'name' => 'Paid Invoices',
            'icon' => 'o-receipt-percent',
            'default_permission' => 'invoices.access',
            'url' => 'paidinvoices.index',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('submodules')->where('url', 'paidinvoices.index')->delete();
    }
};
