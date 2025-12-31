<?php

namespace App\Console\Commands;

use App\Models\Customer;
use App\Models\Nationality;
use App\Models\Province;
use App\Models\City;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Customertype;
use App\Models\Employmentlocation;
use App\Models\Employmentstatus;
class MigrateCustomers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:migrate-customers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate customers from customerimports table to customers table';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $imports = DB::table('customerimports')->where('processed', 'N')->get();
        $count = 0;
        $errors = 0;

        foreach ($imports as $import) {
            try {
                // Check if customer already exists by email
            

                // Generate regnumber with prefix if not already prefixed
                $regnumber = $import->regnumber;
                if (! str_starts_with($regnumber, config('generalutils.registration_prefix'))) {
                    $regnumber = config('generalutils.registration_prefix').$import->regnumber;
                }
                $nationality = Nationality::where('name', $import->nationality)->first();
                $province = Province::where('name', $import->province)->first();
                $city = City::where('name', $import->city)->first();
                $customertype = Customertype::where('name', $import->customertype)->first();
                $employmentlocation = Employmentlocation::where('name', $import->employmentlocation)->first();
                $employmentstatus = Employmentstatus::where('name', $import->employmentstatus)->first();

                Customer::create([
                    'uuid' => Str::uuid()->toString(),
                    'name' => $import->name,
                    'surname' => $import->surname,
                    'regnumber' => $regnumber,
                    'email' => $import->email,
                    'gender' => $import->gender ?? null,
                    'nationality_id' => $nationality->id ?? 230,
                    'province_id' => $province->id ?? 1,
                    'city_id' => $city->id ?? 1,
                    'employmentlocation_id' => $employmentlocation->id ?? 1,
                    'employmentstatus_id' => $employmentstatus->id ?? 1,
                ]);

                DB::table('customerimports')->where('id', $import->id)->update(['processed' => 'Y']);
                $count++;
                $this->info("Migrated: {$import->name} {$import->surname} ({$import->email})");
            } catch (\Exception $e) {
                $errors++;
                $this->error("Error migrating customer ID {$import->id}: {$e->getMessage()}");
            }
        }

        $this->info("Migration completed. Successfully migrated: {$count}, Errors: {$errors}");

        return Command::SUCCESS;
    }
}
