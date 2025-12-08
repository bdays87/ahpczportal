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
        $customers = DB::connection('mysql2')->table('customers')
        ->join('titles','titles.Id','=','customers.TitleId')
        ->join('genders','genders.Id','=','customers.GenderId')
        ->join('customercontacts','customercontacts.CustomerId','=','customers.Id')
        ->select('customers.*','titles.Name as title','genders.Name as gender','customercontacts.Email as email','customercontacts.PrimaryPhone as primaryphone','customercontacts.SecondaryPhone as secondaryphone','customercontacts.Address as contactaddress')->get();
        foreach($customers as $customer){
            $checkcustomer = Customer::where('id',$customer->Id)->first();
            if($checkcustomer){
                continue;
            }
           // $dob = Carbon::createFromFormat('Y-m-d', $customer->Dob);
            Customer::create([
                'id' => $customer->Id,
                'title' => $customer->title,
                'uuid'=>Str::uuid(),
                'name' => $customer->FirstName,
                'surname' => $customer->LastName,
                'previous_name' => $customer->PreviousName,
                'regnumber' => $customer->RegistrationNumber,
                'identificationtype' => 'NATIONAL_ID',
                'identificationnumber' => $customer->IDnumber,
                'gender' => $customer->gender,
                'maritalstatus' => 'SINGLE',
                'email' => $customer->Email,
                'phone' => $customer->Phone,
                'address' => $customer->contactaddress,
                'place_of_birth' => $customer->PlaceofBirth,
                'dob' => $customer->Dob,
                'nationality_id' => $customer->NationalityId,
                'province_id' => $customer->ProvinceId,
                'city_id' => $customer->CityId,
                'employmentlocation_id' => $customer->EmploymentLocationId,
                'employmentstatus_id' => $customer->EmploymentStatusId,
            ]);
            $this->info('Customer migrated: '.$customer->FirstName.' '.$customer->LastName);
        }
    }
}
