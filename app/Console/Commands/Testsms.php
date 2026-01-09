<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class Testsms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:testsms';

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
        $payload = [
            "originator" => "Anixsys",
            "destination" => "263775474661",
            "messageText" => "This is a test message",
            "messageReference" => "R99577E",
            "messageDate" => date('YmdHis'),
            "messageValidity" => date('H:i:s', strtotime('+3 hours')),
            "sendDateTime" => date('H:i:s')
        ];

        // Manually set Basic Auth header to match C# implementation
        // C# equivalent: Convert.ToBase64String(Encoding.ASCII.GetBytes($"{username}:{password}"))
        $username = 'ANIXAPI';
        $password = 'Tanisha1315@';
        $credentials = base64_encode("{$username}:{$password}");

        try {
            $response = Http::withHeaders([
                'Authorization' => "Basic {$credentials}",
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])
                ->withBody(json_encode($payload), 'application/json')
                ->post('https://mobile.esolutions.co.zw/bmg/api/single');

            $statusCode = $response->status();
            $responseBody = $response->body();

            // Check if request was successful
            if ($response->successful()) {
                $responseData = $response->json();

                // Check if response contains messageId (indicates success)
                if (isset($responseData['messageId'])) {
                    $this->info('✓ SMS sent successfully!');
                    $this->newLine();
                    $this->line('Message Details:');
                    $this->line('  Message ID: '.$responseData['messageId']);
                    $this->line('  Status: '.$responseData['status']);
                    $this->line('  Destination: '.$responseData['destination']);
                    $this->line('  Originator: '.$responseData['originator']);
                    $this->line('  Charge: '.$responseData['charge']);
                    $this->line('  Message Reference: '.$responseData['messageReference']);
                    $this->line('  Scheduled: '.($responseData['scheduled'] ? 'Yes' : 'No'));
                    $this->line('  Is Local: '.($responseData['isLocal'] ? 'Yes' : 'No'));

                    return Command::SUCCESS;
                } else {
                    $this->error('✗ Unexpected response format');
                    $this->line('Response: '.$responseBody);
                    return Command::FAILURE;
                }
            } else {
                $this->error('✗ Failed to send SMS');
                $this->error('Status Code: '.$statusCode);
                $this->error('Response: '.$responseBody);

                // Try to parse error response if it's JSON
                $errorData = $response->json();
                if ($errorData && isset($errorData['message'])) {
                    $this->error('Error Message: '.$errorData['message']);
                }

                return Command::FAILURE;
            }
        } catch (\Exception $e) {
            $this->error('✗ Exception occurred: '.$e->getMessage());
            return Command::FAILURE;
        }
    }
}
