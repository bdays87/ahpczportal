<?php

namespace App\Console\Commands;

use App\Models\Customer;
use App\Models\Customerprofession;
use App\Models\Customerregistration;
use App\Models\Profession;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateCustomerRegistrations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:migrate-customer-registrations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate customer registrations from customerregistrationimports table to customerregistrations table';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $imports = DB::table('customerregistrationimports')->where('processed', 'N')->get();
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

                // Check if registration already exists
                $existingRegistration = Customerregistration::where('customer_id', $customer->id)
                    ->where('customerprofession_id', $customerProfession->id)
                    ->where('certificatenumber', $import->certificatenumber)
                    ->first();

                if ($existingRegistration) {
                    $this->warn("Registration already exists for certificate number: {$import->certificatenumber}. Skipping...");
                    DB::table('customerregistrationimports')->where('id', $import->id)->update(['processed' => 'Y']);

                    continue;
                }

                // Parse registration date
                $registrationDate = null;
                if ($import->registrationdate) {
                    try {
                        $registrationDate = Carbon::parse($import->registrationdate)->format('Y-m-d');
                    } catch (\Exception $e) {
                        $this->warn("Invalid registration date format: {$import->registrationdate}. Using null.");
                    }
                }

                Customerregistration::create([
                    'customer_id' => $customer->id,
                    'customerprofession_id' => $customerProfession->id,
                    'status' => $import->status ?? 'APPROVED',
                    'certificatenumber' => $import->certificatenumber,
                    'registrationdate' => $registrationDate,
                    'year' => $registrationDate ? Carbon::parse($registrationDate)->year : date('Y'),
                ]);

                DB::table('customerregistrationimports')->where('id', $import->id)->update(['processed' => 'Y']);
                $count++;
                $this->info("Migrated registration: Customer {$customer->name} - Certificate {$import->certificatenumber}");
            } catch (\Exception $e) {
                $errors++;
                $this->error("Error migrating registration import ID {$import->id}: {$e->getMessage()}");
            }
        }

        $this->info("Migration completed. Successfully migrated: {$count}, Errors: {$errors}");

        return Command::SUCCESS;
    }
}
