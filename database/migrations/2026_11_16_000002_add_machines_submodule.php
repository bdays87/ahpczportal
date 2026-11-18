<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Adds "Machines" to the same menu section as "Data management", pointing at
     * machines.index. Reuses customers.access so it is visible to whoever can
     * already see Customers - no new permission/role setup needed.
     */
    public function up(): void
    {
        if (DB::table('submodules')->where('url', 'machines.index')->exists()) {
            return;
        }

        $moduleId = DB::table('submodules')->where('url', 'datamanagement.index')->value('systemmodule_id');

        if (! $moduleId) {
            return;
        }

        DB::table('submodules')->insert([
            'systemmodule_id' => $moduleId,
            'name' => 'Machines',
            'icon' => 'o-cpu-chip',
            'default_permission' => 'customers.access',
            'url' => 'machines.index',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('submodules')->where('url', 'machines.index')->delete();
    }
};
