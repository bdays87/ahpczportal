<?php

namespace App\Console\Commands;

use App\Models\Document;
use DB;
use Illuminate\Console\Command;

class MigrateDocuments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:migrate-documents';

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
        $documents = DB::connection('mysql2')->table('documentrequirements')->get();
        foreach ($documents as $document) {
            Document::FirstOrCreate([
                'id' => $document->Id,
                'name' => strip_tags(html_entity_decode($document->Name, ENT_QUOTES, 'UTF-8')),
                'group' => $document->group,
            ]);
            $this->info('Document migrated: '.$document->Name);
        }
    }
}
