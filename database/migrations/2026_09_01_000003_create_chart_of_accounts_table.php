<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('chart_of_accounts')) {
            Schema::create('chart_of_accounts', function (Blueprint $table) {
                $table->id();
                $table->string('code')->unique();               // e.g. "1001"
                $table->string('name');                         // e.g. "Cash at Bank"
                $table->text('description')->nullable();
                // Account type: ASSET, LIABILITY, EQUITY, INCOME, EXPENSE
                $table->string('account_type');
                // Sub-type: Current Asset, Fixed Asset, Current Liability, etc.
                $table->string('account_subtype')->nullable();
                $table->foreignId('parent_id')->nullable()->constrained('chart_of_accounts');
                $table->foreignId('cost_center_id')->nullable()->constrained('cost_centers');
                $table->foreignId('currency_id')->nullable()->constrained('currencies');
                // Normal balance side: DEBIT or CREDIT
                $table->string('normal_balance')->default('DEBIT');
                $table->boolean('is_header')->default(false);   // header / summary account
                $table->boolean('allow_direct_posting')->default(true);
                $table->string('status')->default('active');    // active, inactive
                $table->foreignId('createdby')->nullable()->constrained('users');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('chart_of_accounts');
    }
};
