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
        Schema::table('customerprofessions', function (Blueprint $table) {
            // Sage sync tracking fields
            $table->string('sage_sync_status', 20)->nullable()->after('status')
                  ->comment('PENDING, SYNCING, SYNCED, FAILED');
            $table->string('sage_customer_code', 50)->nullable()->after('sage_sync_status')
                  ->comment('Customer code in Sage Evolution');
            $table->text('sage_sync_error')->nullable()->after('sage_customer_code')
                  ->comment('Error message if sync failed');
            $table->timestamp('sage_synced_at')->nullable()->after('sage_sync_error')
                  ->comment('When last synced to Sage');
            
            // Add index for faster queries
            $table->index('sage_sync_status');
            $table->index('sage_customer_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customerprofessions', function (Blueprint $table) {
            $table->dropIndex(['sage_sync_status']);
            $table->dropIndex(['sage_customer_code']);
            
            $table->dropColumn([
                'sage_sync_status',
                'sage_customer_code',
                'sage_sync_error',
                'sage_synced_at',
            ]);
        });
    }
};
