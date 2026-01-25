<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if column already exists
        $columnExists = DB::selectOne("
            SELECT COLUMN_NAME 
            FROM information_schema.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'customerhistoricaldatadocuments' 
            AND COLUMN_NAME = 'customerhistoricaldataprofession_id'
        ");

        // First, add the new column to customerhistoricaldatadocuments if it doesn't exist
        if (! $columnExists) {
            Schema::table('customerhistoricaldatadocuments', function (Blueprint $table) {
                $table->foreignId('customerhistoricaldataprofession_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('customerhistoricaldataprofessions')
                    ->onDelete('cascade')
                    ->name('hist_data_docs_prof_id_foreign');
            });
        } else {
            // Column exists, but check if foreign key exists
            $fkExists = DB::selectOne("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'customerhistoricaldatadocuments' 
                AND COLUMN_NAME = 'customerhistoricaldataprofession_id' 
                AND CONSTRAINT_NAME != 'PRIMARY'
                LIMIT 1
            ");

            if (! $fkExists) {
                // Add foreign key constraint if column exists but constraint doesn't
                DB::statement('ALTER TABLE customerhistoricaldatadocuments ADD CONSTRAINT hist_data_docs_prof_id_foreign FOREIGN KEY (customerhistoricaldataprofession_id) REFERENCES customerhistoricaldataprofessions(id) ON DELETE CASCADE');
            }
        }

        // Migrate existing data to customerhistoricaldataprofessions
        // Get all existing customerhistoricaldata records
        $historicalDataRecords = DB::table('customerhistoricaldata')->get();

        foreach ($historicalDataRecords as $record) {
            // Only create profession record if profession_id exists
            if ($record->profession_id) {
                DB::table('customerhistoricaldataprofessions')->insert([
                    'customerhistoricaldata_id' => $record->id,
                    'profession_id' => $record->profession_id,
                    'registrationnumber' => $record->registrationnumber,
                    'registrationyear' => $record->registrationyear,
                    'practisingcertificatenumber' => $record->practisingcertificatenumber,
                    'registertype_id' => $record->registertype_id,
                    'last_renewal_year' => $record->last_renewal_year ?? null,
                    'last_renewal_year_cdp_points' => $record->last_renewal_year_cdp_points ?? null,
                    'last_renewal_expire_date' => $record->last_renewal_expire_date ?? null,
                    'created_at' => $record->created_at,
                    'updated_at' => $record->updated_at,
                ]);

                // Get the profession record ID for updating documents
                $professionRecordId = DB::getPdo()->lastInsertId();

                // Update documents to reference the profession record (only if column exists)
                $columnExists = DB::selectOne("
                    SELECT COLUMN_NAME 
                    FROM information_schema.COLUMNS 
                    WHERE TABLE_SCHEMA = DATABASE() 
                    AND TABLE_NAME = 'customerhistoricaldatadocuments' 
                    AND COLUMN_NAME = 'customerhistoricaldataprofession_id'
                ");

                if ($columnExists) {
                    DB::table('customerhistoricaldatadocuments')
                        ->where('customerhistoricaldata_id', $record->id)
                        ->update(['customerhistoricaldataprofession_id' => $professionRecordId]);
                }
            }
        }

        // Drop old foreign key and column after data migration
        // Find the actual constraint name from the database
        $constraintResult = DB::selectOne("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'customerhistoricaldatadocuments' 
            AND COLUMN_NAME = 'customerhistoricaldata_id' 
            AND CONSTRAINT_NAME != 'PRIMARY'
            LIMIT 1
        ");

        if ($constraintResult && isset($constraintResult->CONSTRAINT_NAME)) {
            DB::statement("ALTER TABLE customerhistoricaldatadocuments DROP FOREIGN KEY `{$constraintResult->CONSTRAINT_NAME}`");
        }

        // Check if old column exists before dropping
        $oldColumnExists = DB::selectOne("
            SELECT COLUMN_NAME 
            FROM information_schema.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'customerhistoricaldatadocuments' 
            AND COLUMN_NAME = 'customerhistoricaldata_id'
        ");

        if ($oldColumnExists) {
            Schema::table('customerhistoricaldatadocuments', function (Blueprint $table) {
                $table->dropColumn('customerhistoricaldata_id');
            });
        }

        // Remove profession fields from customerhistoricaldata
        // Find actual constraint name from database
        $professionConstraintResult = DB::selectOne("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'customerhistoricaldata' 
            AND COLUMN_NAME = 'profession_id' 
            AND CONSTRAINT_NAME != 'PRIMARY'
            LIMIT 1
        ");

        if ($professionConstraintResult && isset($professionConstraintResult->CONSTRAINT_NAME)) {
            DB::statement("ALTER TABLE customerhistoricaldata DROP FOREIGN KEY `{$professionConstraintResult->CONSTRAINT_NAME}`");
        }

        // Check which columns exist before dropping
        $columnsToDrop = [];
        $existingColumns = DB::select("
            SELECT COLUMN_NAME 
            FROM information_schema.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'customerhistoricaldata'
        ");

        $existingColumnNames = array_map(function ($col) {
            return $col->COLUMN_NAME;
        }, $existingColumns);

        $columnsToCheck = [
            'profession_id',
            'registrationnumber',
            'registrationyear',
            'practisingcertificatenumber',
            'registertype_id',
            'last_renewal_year',
            'last_renewal_year_cdp_points',
            'last_renewal_expire_date',
        ];

        foreach ($columnsToCheck as $column) {
            if (in_array($column, $existingColumnNames)) {
                $columnsToDrop[] = $column;
            }
        }

        if (! empty($columnsToDrop)) {
            Schema::table('customerhistoricaldata', function (Blueprint $table) use ($columnsToDrop) {
                $table->dropColumn($columnsToDrop);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add profession fields back to customerhistoricaldata
        Schema::table('customerhistoricaldata', function (Blueprint $table) {
            $table->foreignId('profession_id')
                ->nullable()
                ->constrained('professions')
                ->name('hist_data_profession_id_foreign');
            $table->string('registrationnumber')->nullable();
            $table->integer('registrationyear')->nullable();
            $table->string('practisingcertificatenumber')->nullable();
            $table->foreignId('registertype_id')
                ->nullable()
                ->constrained('registertypes')
                ->name('hist_data_registertype_id_foreign');
            $table->integer('last_renewal_year')->nullable();
            $table->string('last_renewal_year_cdp_points')->nullable();
            $table->date('last_renewal_expire_date')->nullable();
        });

        // Migrate data back from customerhistoricaldataprofessions
        $professionRecords = DB::table('customerhistoricaldataprofessions')->get();
        foreach ($professionRecords as $profRecord) {
            DB::table('customerhistoricaldata')
                ->where('id', $profRecord->customerhistoricaldata_id)
                ->update([
                    'profession_id' => $profRecord->profession_id,
                    'registrationnumber' => $profRecord->registrationnumber,
                    'registrationyear' => $profRecord->registrationyear,
                    'practisingcertificatenumber' => $profRecord->practisingcertificatenumber,
                    'registertype_id' => $profRecord->registertype_id,
                    'last_renewal_year' => $profRecord->last_renewal_year,
                    'last_renewal_year_cdp_points' => $profRecord->last_renewal_year_cdp_points,
                    'last_renewal_expire_date' => $profRecord->last_renewal_expire_date,
                ]);

            // Update documents back
            DB::table('customerhistoricaldatadocuments')
                ->where('customerhistoricaldataprofession_id', $profRecord->id)
                ->update(['customerhistoricaldata_id' => $profRecord->customerhistoricaldata_id]);
        }

        // Restore customerhistoricaldatadocuments structure
        Schema::table('customerhistoricaldatadocuments', function (Blueprint $table) {
            $table->foreignId('customerhistoricaldata_id')
                ->after('id')
                ->constrained('customerhistoricaldata')
                ->onDelete('cascade')
                ->name('hist_data_docs_hist_data_id_foreign');
            $table->dropForeign(['customerhistoricaldataprofession_id']);
            $table->dropColumn('customerhistoricaldataprofession_id');
        });
    }
};
