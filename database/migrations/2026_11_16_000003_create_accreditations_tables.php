<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Master list of accreditations (e.g. National Certification Programme,
     * SADCAS) and their levels, plus a link from an institution's accreditation
     * to that list. The text name/level columns on the institution row are kept
     * so existing records and public pages keep working.
     */
    public function up(): void
    {
        Schema::create('accreditations', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->string('status')->default('ACTIVE');
            $table->timestamps();
        });

        Schema::create('accreditationlevels', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('accreditation_id');
            $table->string('name');
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('status')->default('ACTIVE');
            $table->timestamps();

            $table->index('accreditation_id');
        });

        Schema::table('otherapplicationinstaccreditations', function (Blueprint $table) {
            $table->unsignedBigInteger('accreditation_id')->nullable()->after('otherapplication_id');
            $table->unsignedBigInteger('accreditationlevel_id')->nullable()->after('accreditation_id');
        });

        // Seed the accreditations we know about; levels are added by admins.
        foreach (['National Certification Programme', 'SADCAS'] as $name) {
            DB::table('accreditations')->insert(['name' => $name, 'status' => 'ACTIVE', 'created_at' => now(), 'updated_at' => now()]);
        }

        // Link accreditations already recorded on institutions, by matching name.
        foreach (DB::table('accreditations')->get() as $a) {
            DB::table('otherapplicationinstaccreditations')
                ->whereNull('accreditation_id')
                ->whereRaw('LOWER(name) = ?', [strtolower($a->name)])
                ->update(['accreditation_id' => $a->id]);
        }

        // Menu entry next to Institutions.
        $moduleId = DB::table('submodules')->where('url', 'institutions.index')->value('systemmodule_id');
        if ($moduleId && ! DB::table('submodules')->where('url', 'accreditations.index')->exists()) {
            DB::table('submodules')->insert([
                'systemmodule_id' => $moduleId,
                'name' => 'Accreditations',
                'icon' => 'o-shield-check',
                'default_permission' => 'institution.access',
                'url' => 'accreditations.index',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('submodules')->where('url', 'accreditations.index')->delete();
        Schema::table('otherapplicationinstaccreditations', function (Blueprint $table) {
            $table->dropColumn(['accreditation_id', 'accreditationlevel_id']);
        });
        Schema::dropIfExists('accreditationlevels');
        Schema::dropIfExists('accreditations');
    }
};
