<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use App\Models\ProfessionTire;
class MigrateProfessionTires extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:migrate-profession-tires';

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
        $response = DB::connection('mysql2')->table('professionrenewaltires')->join('professions','professions.Id','=','professionrenewaltires.ProfessionId')->join('renewaltires','renewaltires.id','=','professionrenewaltires.RenewalTireId')->select('professions.id as profession_id','renewaltires.id as tire_id','professions.Points as required_cdp','professions.Points as minimum_cdp')->get();
        foreach($response as $row){
                ProfessionTire::create([
                    'profession_id'=>$row->profession_id,
                    'tire_id'=>$row->tire_id,
                    'required_cdp'=>$row->required_cdp,
                    'minimum_cdp'=>$row->minimum_cdp,
                ]);
                $this->info('profession_id: '.$row->profession_id.' tire_id: '.$row->tire_id.' required_cdp: '.$row->required_cdp.' minimum_cdp: '.$row->minimum_cdp);
        }
    }
}
