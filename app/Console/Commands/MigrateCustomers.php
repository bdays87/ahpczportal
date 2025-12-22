<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use App\Models\Customer;
use Illuminate\Support\Str;
use Carbon\Carbon;
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
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $customers = DB::table('customerimports')->where('processed', 'N')->select('id','name','surname','regnumber','email')->get();
         foreach($customers as $customer){
           
           // $dob = Carbon::createFromFormat('Y-m-d', $customer->Dob);
           $regnumber = config('generalutils.registration_prefix').$customer->regnumber;
            Customer::create([
                'uuid'=>Str::uuid(),
                'name' => $customer->name,
                'surname' => $customer->surname,
                'regnumber' => $regnumber,
                'email' => $customer->email,
            ]);
            DB::table('customerimports')->where('id', $customer->id)->update(['processed' => 'Y']);
            $this->info('name: '.$customer->name.' surname: '.$customer->surname.' regnumber: '.$customer->regnumber.' email: '.$customer->email);
        }
    }
}
