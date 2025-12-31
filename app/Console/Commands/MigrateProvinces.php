<?php

namespace App\Console\Commands;

use App\Models\City;
use App\Models\Province;
use DB;
use Illuminate\Console\Command;

class MigrateProvinces extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:migrate-provinces';

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
        $provinces = DB::connection('mysql2')->table('provinces')->select('Id', 'Name')->get();
        foreach ($provinces as $province) {
            Province::create([
                'id' => $province->Id,
                'name' => $province->Name,
            ]);
            $cities = DB::connection('mysql2')->table('cities')->select('Id', 'Name', 'ProvinceId')->where('ProvinceId', $province->Id)->get();
            foreach ($cities as $city) {
                City::create([
                    'id' => $city->Id,
                    'name' => $city->Name,
                    'province_id' => $province->Id,
                ]);
            }
        }
    }
}
