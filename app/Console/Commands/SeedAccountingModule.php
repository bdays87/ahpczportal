<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class SeedAccountingModule extends Command
{
    protected $signature = 'db:seed-accounting {--rollback : Rollback the accounting module seeding}';
    
    protected $description = 'Seed accounting module data from SQL file (works via SSH)';

    public function handle()
    {
        if ($this->option('rollback')) {
            return $this->rollback();
        }

        $sqlFile = database_path('seeders/accounting_module_seeder.sql');

        if (!File::exists($sqlFile)) {
            $this->error("❌ SQL file not found: $sqlFile");
            return 1;
        }

        $this->info('🚀 Seeding Accounting Module...');
        $this->newLine();

        try {
            // Read the SQL file
            $sql = File::get($sqlFile);

            // Remove comments and split by semicolons
            $sql = preg_replace('/--.*$/m', '', $sql); // Remove single-line comments
            $sql = preg_replace('/\/\*.*?\*\//s', '', $sql); // Remove multi-line comments
            
            // Split into individual statements
            $statements = array_filter(
                array_map('trim', explode(';', $sql)),
                fn($stmt) => !empty($stmt)
            );

            $this->info("📝 Found " . count($statements) . " SQL statements");
            $this->newLine();

            DB::beginTransaction();

            $executed = 0;
            $errors = 0;

            foreach ($statements as $index => $statement) {
                try {
                    // Skip empty statements
                    if (empty(trim($statement))) {
                        continue;
                    }

                    DB::statement($statement);
                    $executed++;

                    // Show progress for major statements
                    if (stripos($statement, 'INSERT INTO') !== false) {
                        if (stripos($statement, 'systemmodules') !== false) {
                            $this->line("  ✓ Created Accounting system module");
                        } elseif (stripos($statement, 'submodules') !== false) {
                            $this->line("  ✓ Created accounting submodules");
                        } elseif (stripos($statement, 'permissions') !== false) {
                            $count = substr_count(strtoupper($statement), 'ACCOUNTING.');
                            if ($count > 0) {
                                $this->line("  ✓ Created $count accounting permissions");
                            }
                        }
                    }
                } catch (\Exception $e) {
                    $errors++;
                    $this->warn("  ⚠️  Statement " . ($index + 1) . " failed: " . $e->getMessage());
                }
            }

            if ($errors > 0) {
                $this->newLine();
                $this->warn("⚠️  Some statements failed. Rolling back...");
                DB::rollBack();
                return 1;
            }

            DB::commit();

            $this->newLine();
            $this->info("✅ Successfully executed $executed SQL statements");
            $this->newLine();

            // Show summary
            $this->showSummary();

            return 0;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('❌ Error: ' . $e->getMessage());
            $this->error('Trace: ' . $e->getTraceAsString());
            return 1;
        }
    }

    private function showSummary()
    {
        try {
            // Count accounting modules
            $module = DB::table('systemmodules')
                ->where('name', 'Accounting')
                ->first();

            if ($module) {
                $submodules = DB::table('submodules')
                    ->where('systemmodule_id', $module->id)
                    ->count();

                $permissions = DB::table('permissions')
                    ->where('name', 'LIKE', 'accounting.%')
                    ->count();

                $this->info("📊 Summary:");
                $this->info("  - System Module: Accounting (ID: {$module->id})");
                $this->info("  - Submodules: $submodules");
                $this->info("  - Permissions: $permissions");
                $this->newLine();
                $this->info("🎉 Accounting module seeded successfully!");
            } else {
                $this->warn("⚠️  Accounting module not found after seeding");
            }

        } catch (\Exception $e) {
            $this->warn("⚠️  Could not generate summary: " . $e->getMessage());
        }
    }

    private function rollback()
    {
        $this->warn('🔄 Rolling back accounting module...');
        $this->newLine();

        if (!$this->confirm('This will delete all accounting module data. Continue?')) {
            $this->info('❌ Rollback cancelled.');
            return 0;
        }

        try {
            DB::beginTransaction();

            // Get accounting module ID
            $module = DB::table('systemmodules')
                ->where('name', 'Accounting')
                ->first();

            if (!$module) {
                $this->warn('⚠️  Accounting module not found. Nothing to rollback.');
                return 0;
            }

            $this->info("Found Accounting module (ID: {$module->id})");

            // Delete permissions
            $permissions = DB::table('permissions')
                ->where('name', 'LIKE', 'accounting.%')
                ->delete();
            $this->line("  ✓ Deleted $permissions permissions");

            // Delete submodules
            $submodules = DB::table('submodules')
                ->where('systemmodule_id', $module->id)
                ->delete();
            $this->line("  ✓ Deleted $submodules submodules");

            // Delete system module
            DB::table('systemmodules')
                ->where('id', $module->id)
                ->delete();
            $this->line("  ✓ Deleted system module");

            DB::commit();

            $this->newLine();
            $this->info('✅ Accounting module rolled back successfully!');

            return 0;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('❌ Rollback failed: ' . $e->getMessage());
            return 1;
        }
    }
}
