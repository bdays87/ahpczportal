<?php

namespace App\Livewire\Admin;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Datamanagement extends Component
{
    public $breadcrumbs = [];
    public $selectedTab = 'professionimports-tab';

    /** Output captured from the last command that was run. */
    public string $commandOutput = '';

    /** Label of the last command that was run (for the console header). */
    public string $lastRun = '';

    private array $templates = [
        'professions' => [
            'headers'  => ['name', 'prefix'],
            'sample'   => ['Medical Laboratory Scientist', 'MLS'],
            'filename' => 'professions_template.csv',
        ],
        'customers' => [
            'headers'  => ['name', 'surname', 'regnumber', 'gender', 'email'],
            'sample'   => ['John', 'Doe', 'MLCSCZ/2020/001', 'MALE', 'john.doe@example.com'],
            'filename' => 'customers_template.csv',
        ],
        'users' => [
            'headers'  => ['name', 'surname', 'regnumber', 'email', 'password'],
            'sample'   => ['John', 'Doe', 'MLCSCZ/2020/001', 'john.doe@example.com', 'Password@123'],
            'filename' => 'users_template.csv',
        ],
        'customerprofessions' => [
            'headers'  => ['regnumber', 'prefix', 'status', 'tire', 'customertype'],
            'sample'   => ['MLCSCZ/2020/001', 'MLS', 'APPROVED', 'Tier 1', 'Practitioner'],
            'filename' => 'customerprofessions_template.csv',
        ],
        'customerregistrations' => [
            'headers'  => ['regnumber', 'prefix', 'certificatenumber', 'registrationdate', 'status'],
            'sample'   => ['MLCSCZ/2020/001', 'MLS', 'CERT/2020/001', '2020-01-01', 'APPROVED'],
            'filename' => 'customerregistrations_template.csv',
        ],
        'customerapplications' => [
            'headers'  => ['regnumber', 'prefix', 'applicationtype', 'registertype', 'certificatenumber', 'registrationdate', 'certificateexpirydate', 'year', 'status'],
            'sample'   => ['MLCSCZ/2020/001', 'MLS', 'RENEWAL', 'Main Register', 'CERT/2024/001', '2024-01-01', '2024-12-31', '2024', 'APPROVED'],
            'filename' => 'customerapplications_template.csv',
        ],
        'customercdp' => [
            'headers'  => ['regnumber', 'points', 'year'],
            'sample'   => ['MLCSCZ/2020/001', '50', '2024'],
            'filename' => 'customercdp_template.csv',
        ],
        'institutions' => [
            'headers'  => ['Institution Name', 'Institution Type', 'Institution Sub Type', 'Nature Of Institution', 'Institution Class', 'Registration No', 'Registration Date', 'Phone Numbers', 'Email Addresses', 'Address Line 1', 'Address Line 2', 'Address Line 3', 'Address Line 4', 'City', 'ProvinceName'],
            'sample'   => ['Sample Laboratory', 'Laboratory', 'Multidisciplinary Laboratory', 'Medical', 'Class A', 'LAB001', '01 January 2020', 'ZW-0772000000', 'info@sample.co.zw', '1 Sample Street', '', '', '', 'Harare', 'Harare'],
            'filename' => 'institutions_template.csv',
        ],
    ];

    public function mount()
    {
        $this->breadcrumbs = [
            ['label' => 'Dashboard', 'icon' => 'o-home', 'link' => route('dashboard')],
            ['label' => 'Data Management'],
        ];
    }

    public function downloadtemplate(string $type): StreamedResponse
    {
        $template = $this->templates[$type] ?? null;

        if (! $template) {
            abort(404, 'Template not found');
        }

        return response()->streamDownload(function () use ($template) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $template['headers']);
            fputcsv($handle, $template['sample']);
            fclose($handle);
        }, $template['filename'], ['Content-Type' => 'text/csv']);
    }

    /**
     * The migration/processing commands available from this screen,
     * in the recommended order to run them. This doubles as the
     * security allow-list — only these signatures may be executed.
     */
    public function migrations(): array
    {
        return [
            [
                'signature' => 'app:migrate-customers',
                'label' => 'Migrate Customers',
                'description' => 'Move imported customers from the staging table into the live Customers list.',
                'table' => 'customerimports',
            ],
            [
                'signature' => 'app:migrate-customer-profession',
                'label' => 'Migrate Customer Professions',
                'description' => 'Create customer profession records from the professions staging table.',
                'table' => 'customerprofessionimports',
            ],
            [
                'signature' => 'app:migrate-customer-registrations',
                'label' => 'Migrate Customer Registrations',
                'description' => 'Create customer registration records from the registrations staging table.',
                'table' => 'customerregistrationimports',
            ],
            [
                'signature' => 'app:migrate-customer-applications',
                'label' => 'Migrate Customer Applications',
                'description' => 'Create customer application records from the applications staging table.',
                'table' => 'customerapplicationimports',
            ],
            [
                'signature' => 'app:backfill-customer-profession-uuids',
                'label' => 'Backfill Profession UUIDs',
                'description' => 'Generate missing UUIDs for customer profession records.',
                'table' => null,
            ],
        ];
    }

    /** Number of un-processed rows still waiting in a staging table. */
    public function pendingCount(?string $table): ?int
    {
        if (! $table) {
            return null;
        }

        try {
            return DB::table($table)->where('processed', 'N')->count();
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Run one of the allow-listed Artisan commands and capture its output
     * so the user can see what happened, without using a terminal.
     */
    public function runMigration(string $signature): void
    {
        $allowed = array_column($this->migrations(), 'signature');

        if (! in_array($signature, $allowed, true)) {
            $this->commandOutput = "Command not allowed: {$signature}";

            return;
        }

        $this->commandOutput = '';

        try {
            // Give long-running migrations room to finish.
            @set_time_limit(0);

            Artisan::call($signature);
            $output = trim(Artisan::output());
            $this->commandOutput = $output !== '' ? $output : 'Command finished with no output.';
        } catch (\Throwable $e) {
            $this->commandOutput = 'ERROR: '.$e->getMessage();
        }

        $this->lastRun = 'php artisan '.$signature.'  •  '.now()->format('Y-m-d H:i:s');
    }

    public function render()
    {
        return view('livewire.admin.datamanagement', [
            'migrations' => $this->migrations(),
        ]);
    }
}
