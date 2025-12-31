<?php

namespace App\Console\Commands;

use App\Models\Customer;
use App\Models\Customeruser;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MigrateUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:migrate-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate users from customeruserimports table to users and customerusers tables';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $imports = DB::table('customeruserimports')->where('processed', 'N')->get();
        $count = 0;
        $errors = 0;

        foreach ($imports as $import) {
            try {
                // Find customer by regnumber (with or without prefix)
                $regnumber = $import->regnumber;
                $prefix = config('generalutils.registration_prefix');

                $customer = Customer::where('regnumber', $regnumber)
                    ->orWhere('regnumber', $prefix.$regnumber)
                    ->orWhere('regnumber', str_replace($prefix, '', $regnumber))
                    ->first();

                if (! $customer) {
                    $this->warn("Customer not found for regnumber: {$regnumber}. Skipping...");

                    continue;
                }

                // Check if user already exists
                $existingUser = User::where('email', $import->email)->first();
                if ($existingUser) {
                    // Check if customeruser relationship already exists
                    $existingCustomerUser = Customeruser::where('customer_id', $customer->id)
                        ->where('user_id', $existingUser->id)
                        ->first();

                    if (! $existingCustomerUser) {
                        Customeruser::create([
                            'customer_id' => $customer->id,
                            'user_id' => $existingUser->id,
                        ]);
                        $this->info("Linked existing user to customer: {$customer->name} {$customer->surname}");
                    }
                } else {
                    // Create new user
                    $user = User::create([
                        'uuid' => Str::uuid()->toString(),
                        'name' => $import->name,
                        'surname' => $import->surname,
                        'email' => $import->email,
                        'phone' => '123456789',
                        'password' => $import->password,
                        'accounttype_id' => 2, // Default customer account type
                    ]);

                    // Create customeruser relationship
                    Customeruser::create([
                        'customer_id' => $customer->id,
                        'user_id' => $user->id,
                    ]);

                    $this->info("Created user and linked to customer: {$customer->name} {$customer->surname}");
                }

                DB::table('customeruserimports')->where('id', $import->id)->update(['processed' => 'Y']);
                $count++;
            } catch (\Exception $e) {
                $errors++;
                $this->error("Error migrating user import ID {$import->id}: {$e->getMessage()}");
            }
        }

        $this->info("Migration completed. Successfully migrated: {$count}, Errors: {$errors}");

        return Command::SUCCESS;
    }
}
