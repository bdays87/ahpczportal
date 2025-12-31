<?php

namespace App\Console\Commands;

use App\Models\Customer;
use App\Models\Customerprofession;
use App\Models\Profession;
use App\Models\Customertype;
use App\Models\Tire;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Migratecustomerprofession extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:migrate-customer-profession';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate customer professions from customerprofessionimports table to customerprofessions table';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $imports = DB::table('customerprofessionimports')->where('processed', 'N')->get();
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
                $customertype = Customertype::where('name', $import->customertype)->first();
                if (! $customertype) {
                    $this->warn("Customertype not found for name: {$import->customertype}. Skipping...");

                    continue;
                }

                // Check if customerprofession already exists
                $existingCustomerProfession = Customerprofession::where('customer_id', $customer->id)
                    ->where('profession_id', $profession->id)
                    ->where('status', '!=', 'REJECTED')
                    ->first();

                if ($existingCustomerProfession) {
                    $this->warn("Customer profession already exists for customer ID {$customer->id} and profession ID {$profession->id}. Skipping...");
                    DB::table('customerprofessionimports')->where('id', $import->id)->update(['processed' => 'Y']);

                    continue;
                }

                // Get tire_id if provided
                $tireId = null;
                if ($import->tire) {
                    $tire = Tire::where('name', $import->tire)->first();
                    if ($tire) {
                        $tireId = $tire->id;
                    }else{
                        $tireId=1;
                    }
                }

                Customerprofession::create([
                    'customer_id' => $customer->id,
                    'profession_id' => $profession->id,
                    'customertype_id' => $customertype->id,
                    'tire_id' => $tireId,
                    'uuid' => Str::uuid()->toString(),
                ]);

                DB::table('customerprofessionimports')->where('id', $import->id)->update(['processed' => 'Y']);
                $count++;
                $this->info("Migrated customer profession: Customer {$customer->name} - Profession {$profession->name}");
            } catch (\Exception $e) {
                $errors++;
                $this->error("Error migrating customer profession import ID {$import->id}: {$e->getMessage()}");
            }
        }

        $this->info("Migration completed. Successfully migrated: {$count}, Errors: {$errors}");

        return Command::SUCCESS;
    }
}
