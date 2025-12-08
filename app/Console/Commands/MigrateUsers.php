<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use App\Models\User;
use Illuminate\Support\Str;
use App\Models\Customeruser;
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
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
       $customers = DB::connection('mysql2')->table('customers')->where('RawPassword','!=',null)->get();
        foreach($customers as $customer){
            $checkuser = DB::connection('mysql2')->table('aspnetusers')->where('Name',$customer->FirstName)->where('Surname',$customer->LastName)->first();
            if($checkuser){
          
            $user = User::UpdateOrCreate([
                'email' => $checkuser->Email,
            ],[
                'uuid'=>Str::uuid(),
                'name' => $customer->FirstName,
                'surname' => $customer->LastName,
                'phone' => $customer->Phone??'0000000000',
                'accounttype_id' => $customer->CustomerTypeId==1 ? 2 : 3,
                'password' => $customer->RawPassword,
                
            ]);
            Customeruser::FirstOrCreate([
                'customer_id' => $customer->Id,
                'user_id' => $user->id,
            ]);
            $this->info('User migrated: '.$customer->FirstName.' '.$customer->LastName);
            }else{
                $this->error('User not found: '.$customer->FirstName.' '.$customer->LastName);
            }
    }
    }
}
