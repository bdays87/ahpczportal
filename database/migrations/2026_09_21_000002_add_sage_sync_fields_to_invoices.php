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
        Schema::table('invoices', function (Blueprint $table) {
            // Sage sync tracking fields
            $table->string('sage_sync_status', 20)->nullable()->after('status')
                  ->comment('PENDING, SYNCING, SYNCED, FAILED');
            $table->string('sage_invoice_number', 50)->nullable()->after('sage_sync_status')
                  ->comment('Invoice number in Sage Evolution');
            $table->text('sage_sync_error')->nullable()->after('sage_invoice_number')
                  ->comment('Error message if sync failed');
            $table->timestamp('sage_synced_at')->nullable()->after('sage_sync_error')
                  ->comment('When last synced to Sage');
            
            // Sage invoice data fields (received from Sage)
            $table->decimal('sage_total_excl_tax', 15, 2)->nullable()->after('sage_synced_at');
            $table->decimal('sage_total_tax', 15, 2)->nullable()->after('sage_total_excl_tax');
            $table->decimal('sage_total_incl_tax', 15, 2)->nullable()->after('sage_total_tax');
            $table->decimal('sage_outstanding', 15, 2)->nullable()->after('sage_total_incl_tax')
                  ->comment('Current outstanding amount from Sage');
            $table->string('sage_status', 50)->nullable()->after('sage_outstanding')
                  ->comment('Invoice status in Sage (Unprocessed, Paid, etc.)');
            $table->timestamp('sage_invoice_date')->nullable()->after('sage_status');
            $table->timestamp('sage_last_updated_at')->nullable()->after('sage_invoice_date')
                  ->comment('Last time Sage pushed an update');
            
            // Add indexes
            $table->index('sage_sync_status');
            $table->index('sage_invoice_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropIndex(['sage_sync_status']);
            $table->dropIndex(['sage_invoice_number']);
            
            $table->dropColumn([
                'sage_sync_status',
                'sage_invoice_number',
                'sage_sync_error',
                'sage_synced_at',
                'sage_total_excl_tax',
                'sage_total_tax',
                'sage_total_incl_tax',
                'sage_outstanding',
                'sage_status',
                'sage_invoice_date',
                'sage_last_updated_at',
            ]);
        });
    }
};
