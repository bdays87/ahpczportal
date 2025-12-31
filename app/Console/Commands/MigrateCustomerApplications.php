<?php

namespace App\Console\Commands;

use App\Models\Applicationtype;
use App\Models\Customer;
use App\Models\Customerapplication;
use App\Models\Customerprofession;
use App\Models\Profession;
use App\Models\Registertype;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
class MigrateCustomerApplications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:migrate-customer-applications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate customer applications from customerapplicationimports table to customerapplications table';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $imports = DB::table('customerapplicationimports')->where('processed', 'N')->get();
        $count = 0;
        $errors = 0;

        foreach ($imports as $import) {
            try {
                // Find customer by regnumber
                $regnumber = $import->regnumber;
                $prefix = config('generalutils.registration_prefix');

                $customer = Customer::where('regnumber', $regnumber)
                    ->orWhere('regnumber', $prefix.$regnumber)
                    ->orWhere('regnumber', $regnumber)
                    ->first();

                if (! $customer) {
                    $this->warn("Customer not found for regnumber: {$regnumber}. Skipping...");

                    continue;
                }

                // Find profession by prefix
                $profession = Profession::where('prefix', $import->prefix)->first();
                if (! $profession) {
                    $this->warn("Profession not found for prefix: {$import->prefix}. Skipping...");

                    continue;
                }

                // Find customerprofession
                $customerProfession = Customerprofession::where('customer_id', $customer->id)
                    ->where('profession_id', $profession->id)
                    ->first();

                if (! $customerProfession) {
                    $this->warn("Customer profession not found for customer ID {$customer->id} and profession ID {$profession->id}. Skipping...");

                    continue;
                }

                // Find registertype
                $registerType = Registertype::where('name', $import->registertype)->first();
                if (! $registerType) {
                    $this->warn("Register type not found: {$import->registertype}. Using default...");
                    $registerType = Registertype::first(); // Use first available or handle differently
                    if (! $registerType) {
                        $this->error('No register types available. Skipping...');

                        continue;
                    }
                }

                // Find applicationtype if provided
                $applicationType = null;
                if ($import->applicationtype) {
                    $applicationType = Applicationtype::where('name', strtoupper($import->applicationtype))->first();
                }

                // Check if application already exists
                $existingApplication = Customerapplication::where('customer_id', $customer->id)
                    ->where('customerprofession_id', $customerProfession->id)
                    ->where('certificate_number', $import->certificatenumber)
                    ->first();

                if ($existingApplication) {
                    $this->warn("Application already exists for certificate number: {$import->certificatenumber}. Skipping...");
                    DB::table('customerapplicationimports')->where('id', $import->id)->update(['processed' => 'Y']);

                    continue;
                }

                // Parse dates
                $registrationDate = null;
                $certificateExpiryDate = null;

                if ($import->registrationdate) {
                    try {
                        $registrationDate = Carbon::parse($import->registrationdate)->format('Y-m-d');
                    } catch (\Exception $e) {
                        $this->warn("Invalid registration date format: {$import->registrationdate}");
                    }
                }

                if ($import->certificateexpirydate) {
                    try {
                        $certificateExpiryDate = Carbon::parse($import->certificateexpirydate)->format('Y-m-d');
                    } catch (\Exception $e) {
                        $this->warn("Invalid certificate expiry date format: {$import->certificateexpirydate}");
                    }
                }

                Customerapplication::create([
                    'customer_id' => $customer->id,
                    'customerprofession_id' => $customerProfession->id,
                    'registertype_id' => $registerType->id,
                    'applicationtype_id' => $applicationType?->id,
                    'status' => $import->status ?? 'APPROVED',
                    'certificate_number' => $import->certificatenumber,
                    'registration_date' => $registrationDate,
                    'certificate_expiry_date' => $certificateExpiryDate,
                    'uuid' => Str::uuid(),
                    'year' => $import->year ?? ($registrationDate ? Carbon::parse($registrationDate)->year : date('Y')),
                ]);

                DB::table('customerapplicationimports')->where('id', $import->id)->update(['processed' => 'Y']);
                $count++;
                $this->info("Migrated application: Customer {$customer->name} - Certificate {$import->certificatenumber}");
            } catch (\Exception $e) {
                $errors++;
                $this->error("Error migrating application import ID {$import->id}: {$e->getMessage()}");
            }
        }

        $this->info("Migration completed. Successfully migrated: {$count}, Errors: {$errors}");

        return Command::SUCCESS;
    }
}
