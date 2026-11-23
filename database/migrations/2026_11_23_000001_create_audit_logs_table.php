<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Audit log: who changed what, when, from where, with the record as it was before (and after).
     * Every insert, update and delete made through the app is written here (see app/Audit), plus sign-ins.
     */
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('batch_id', 36)->nullable()->index();   // rows changed by the same statement share this
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('username', 120)->nullable()->index();  // username / registration number / email, as of that moment
            $table->string('user_name', 190)->nullable();          // the person's full name
            $table->string('action', 30)->index();                 // created, updated, deleted, restored, login, logout, login_failed ...
            $table->string('table_name', 120)->nullable()->index();
            $table->string('record_id', 64)->nullable()->index();
            $table->json('old_values')->nullable();                // before (changed columns only for updates; whole row for deletes)
            $table->json('new_values')->nullable();                // after
            $table->string('operation', 255)->nullable();          // e.g. Admin\Banktransactions::saveClaimCustomer
            $table->string('ip', 64)->nullable();
            $table->string('http_method', 10)->nullable();
            $table->string('url', 500)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->useCurrent()->index();
        });

        // Menu entry next to Data management, visible to whoever can see Data management
        $moduleId = DB::table('submodules')->where('url', 'datamanagement.index')->value('systemmodule_id');
        if ($moduleId && ! DB::table('submodules')->where('url', 'auditlogs.index')->exists()) {
            DB::table('submodules')->insert([
                'systemmodule_id' => $moduleId,
                'name' => 'Audit log',
                'icon' => 'o-clipboard-document-list',
                'default_permission' => 'datamanagement.access',
                'url' => 'auditlogs.index',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('submodules')->where('url', 'auditlogs.index')->delete();
        Schema::dropIfExists('audit_logs');
    }
};
