<?php

namespace App\Livewire\Admin;

use App\Interfaces\icustomercontactreportInterface;
use App\Models\Profession;
use App\Models\Province;
use App\Models\Registertype;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

class Customercontactreport extends Component
{
    use Toast, WithPagination;

    public string $tab = 'email';

    public $search;

    public $compliance;

    public $province_id;

    public $profession_id;

    public $registertype_id;

    public $year;

    public array $selected = [];

    // Email composer
    public bool $emailModal = false;

    public $emailSubject;

    public $emailBody;

    // SMS composer
    public bool $smsModal = false;

    public $smsBody;

    public $breadcrumbs = [];

    protected $contactRepo;

    public function boot(icustomercontactreportInterface $contactRepo): void
    {
        $this->contactRepo = $contactRepo;
    }

    public function mount(): void
    {
        $this->year = date('Y');
        $this->breadcrumbs = [
            ['label' => 'Dashboard', 'icon' => 'o-home', 'link' => route('dashboard')],
            ['label' => 'Customer Contacts'],
        ];
    }

    /**
     * Build the filter array passed to the repository.
     * `channel` mirrors the active tab so each tab only shows usable contacts.
     */
    private function filters(bool $withSelection = false): array
    {
        $filters = [
            'search' => $this->search,
            'compliance' => $this->compliance,
            'province_id' => $this->province_id,
            'profession_id' => $this->profession_id,
            'registertype_id' => $this->registertype_id,
            'year' => $this->year,
            'channel' => $this->tab,
        ];

        if ($withSelection && ! empty($this->selected)) {
            $filters['selected'] = $this->selected;
        }

        return $filters;
    }

    public function updated($property): void
    {
        if (in_array($property, ['search', 'compliance', 'province_id', 'profession_id', 'registertype_id', 'year', 'tab'])) {
            $this->resetPage();
            $this->selected = [];
        }
    }

    public function setTab(string $tab): void
    {
        $this->tab = $tab;
        $this->resetPage();
        $this->selected = [];
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'compliance', 'province_id', 'profession_id', 'registertype_id', 'selected']);
        $this->year = date('Y');
        $this->resetPage();
    }

    public function complianceOptions(): array
    {
        return [
            ['id' => '', 'name' => 'All'],
            ['id' => 'compliant', 'name' => 'Compliant'],
            ['id' => 'noncompliant', 'name' => 'Non-compliant'],
        ];
    }

    public function headers(): array
    {
        $headers = [
            ['key' => 'selection', 'label' => '', 'sortable' => false],
            ['key' => 'regnumber', 'label' => 'Reg #'],
            ['key' => 'fullname', 'label' => 'Name'],
        ];

        if ($this->tab === 'email') {
            $headers[] = ['key' => 'email', 'label' => 'Email'];
        } else {
            $headers[] = ['key' => 'phone', 'label' => 'Phone'];
        }

        $headers[] = ['key' => 'province', 'label' => 'Province'];
        $headers[] = ['key' => 'compliance', 'label' => 'Status'];

        return $headers;
    }

    /* ---------------------------------------------------------------------
     |  Exports
     * ------------------------------------------------------------------- */

    private function exportRows()
    {
        return $this->contactRepo->getContactsList($this->filters(true));
    }

    private function exportColumns(): array
    {
        return ['Reg Number', 'First Name', 'Surname', 'Email', 'Phone', 'Province', 'City', 'Compliance Status'];
    }

    private function rowValues($customer): array
    {
        return [
            $customer->regnumber ?? 'N/A',
            $customer->name ?? '',
            $customer->surname ?? '',
            $customer->email ?? '',
            $customer->phone ?? '',
            $customer->province?->name ?? 'N/A',
            $customer->city?->name ?? 'N/A',
            $customer->is_compliant ? 'Compliant' : 'Non-compliant',
        ];
    }

    public function exportCsv()
    {
        $rows = $this->exportRows();
        if ($rows->isEmpty()) {
            $this->warning('No contacts to export.');

            return null;
        }

        $filename = 'customer_contacts_'.date('Y-m-d_His').'.csv';
        $filepath = storage_path('app/public/'.$filename);
        $file = fopen($filepath, 'w');

        fputcsv($file, $this->exportColumns());
        foreach ($rows as $customer) {
            fputcsv($file, $this->rowValues($customer));
        }
        fclose($file);

        return response()->download($filepath)->deleteFileAfterSend(true);
    }

    public function exportExcel()
    {
        $rows = $this->exportRows();
        if ($rows->isEmpty()) {
            $this->warning('No contacts to export.');

            return null;
        }

        $title = ($this->compliance === 'noncompliant' ? 'Non-compliant ' : '').'Customer Contacts';

        $html = '<html><head><meta charset="UTF-8"></head><body>';
        $html .= '<table border="1" cellspacing="0" cellpadding="6" style="border-collapse:collapse;font-family:Arial,sans-serif;font-size:12px;">';
        $html .= '<thead><tr>';
        foreach ($this->exportColumns() as $col) {
            $html .= '<th style="background-color:#1d4ed8;color:#ffffff;font-weight:bold;text-align:left;border:1px solid #1e40af;">'.e($col).'</th>';
        }
        $html .= '</tr></thead><tbody>';
        foreach ($rows as $customer) {
            $html .= '<tr>';
            foreach ($this->rowValues($customer) as $value) {
                $html .= '<td style="border:1px solid #cccccc;">'.e($value).'</td>';
            }
            $html .= '</tr>';
        }
        $html .= '</tbody></table></body></html>';

        $filename = 'customer_contacts_'.date('Y-m-d_His').'.xls';
        $filepath = storage_path('app/public/'.$filename);
        file_put_contents($filepath, $html);

        return response()->download($filepath, $filename, [
            'Content-Type' => 'application/vnd.ms-excel',
        ])->deleteFileAfterSend(true);
    }

    public function exportPdf()
    {
        $rows = $this->exportRows();
        if ($rows->isEmpty()) {
            $this->warning('No contacts to export.');

            return null;
        }

        $title = ($this->compliance === 'noncompliant' ? 'Non-compliant ' : '').'Customer Contacts';

        $pdf = Pdf::loadView('exports.customercontacts', [
            'title' => $title,
            'columns' => $this->exportColumns(),
            'rows' => $rows->map(fn ($c) => $this->rowValues($c)),
            'generatedAt' => now()->format('d M Y H:i'),
        ])->setPaper('a4', 'landscape');

        return response()->streamDownload(
            fn () => print($pdf->stream()),
            'customer_contacts_'.date('Y-m-d_His').'.pdf'
        );
    }

    /* ---------------------------------------------------------------------
     |  Sending
     * ------------------------------------------------------------------- */

    public function openEmailModal(): void
    {
        $this->reset(['emailSubject', 'emailBody']);
        $this->emailModal = true;
    }

    public function sendEmail()
    {
        $this->validate([
            'emailSubject' => 'required|string|max:255',
            'emailBody' => 'required|string',
        ]);

        $response = $this->contactRepo->sendBulkEmail($this->filters(true), $this->emailSubject, $this->emailBody);

        if ($response['status'] === 'success') {
            $this->success($response['message']);
        } else {
            $this->error($response['message']);
        }
        $this->emailModal = false;
    }

    public function openSmsModal(): void
    {
        $this->reset(['smsBody']);
        $this->smsModal = true;
    }

    public function sendSms()
    {
        $this->validate([
            'smsBody' => 'required|string|max:480',
        ]);

        $response = $this->contactRepo->sendBulkSms($this->filters(true), $this->smsBody);

        if ($response['status'] === 'success') {
            $this->success($response['message']);
        } else {
            $this->error($response['message']);
        }
        $this->smsModal = false;
    }

    public function render()
    {
        return view('livewire.admin.customercontactreport', [
            'contacts' => $this->contactRepo->getContacts($this->filters(true)),
            'summary' => $this->contactRepo->getSummary($this->filters()),
            'headers' => $this->headers(),
            'provinces' => Province::orderBy('name')->get(),
            'professions' => Profession::orderBy('name')->get(),
            'registertypes' => Registertype::orderBy('name')->get(),
            'complianceOptions' => $this->complianceOptions(),
        ]);
    }
}
