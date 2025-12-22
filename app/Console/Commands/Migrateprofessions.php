<?php

namespace App\Console\Commands;

use DB;
use Illuminate\Console\Command;
use App\Models\Profession;
class Migrateprofessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:migrateprofessions';

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
        $professions = DB::table('professionimports')->where('proceeded', 'N')->select('id','name','prefix')->get();
        foreach($professions as $profession){
           Profession::create([
                'name' => $profession->name,
                'prefix' => $profession->prefix               
            ]);
            DB::table('professionimports')->where('id', $profession->id)->update(['proceeded' => 'Y']);
          $this->info('name: '.$profession->name.' prefix: '.$profession->prefix);
        }
    }
}
