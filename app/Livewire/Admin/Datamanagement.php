<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Datamanagement extends Component
{
    public $breadcrumbs = [];
    public $selectedTab = 'professionimports-tab';

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

    public function render()
    {
        return view('livewire.admin.datamanagement');
    }
}
