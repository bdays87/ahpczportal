<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-email';

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
        $email = new \SendGrid\Mail\Mail;
        $email->setFrom(config('mail.from.address'), config('mail.from.name'));
        $email->setSubject('Test Email');
        $email->addTo('misib@anixsys.co.zw');
        $email->addContent('text/html', 'This is a test email');

        $sendgrid = new \SendGrid(config('services.sendgrid.api_key'));
        $response = $sendgrid->send($email);
        dd($response);
    }
}
