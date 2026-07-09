<?php

namespace App\Livewire\Admin\Notifications;

use App\Interfaces\ismsbroadcastInterface;
use App\Models\City;
use App\Models\Profession;
use App\Models\Province;
use App\Models\Registertype;
use Livewire\Component;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;

class SmsbroadcastManagement extends Component
{
    use Toast, WithFileUploads;

    protected $broadcastRepo;

    public $breadcrumbs = [];
    public $selectedTab = 'campaigns';

    // Credits
    public $credit_amount;
    public $credit_description;
    public $addCreditsModal = false;

    // Campaign
    public $campaign_name;
    public $campaign_message;
    public $contact_source = 'db'; // 'db' or 'file'
    public $campaign_provider = ''; // overrides SMS_PROVIDER per campaign
    public $test_numbers = ''; // comma-separated test numbers
    public $filter_compliance;
    public $filter_profession_id;
    public $filter_registertype_id;
    public $filter_province_id;
    public $filter_city_id;
    public $createCampaignModal = false;
    public $viewCampaignModal = false;
    public $selectedCampaign = null;

    // Test SMS
    public $testmodal = false;
    public $test_phone = '';
    public $test_message = '';

    // TXT contact import
    public $contactfile = null;
    public $importedcontacts = [];
    public $importmodal = false;

    public function boot(ismsbroadcastInterface $broadcastRepo): void
    {
        $this->broadcastRepo = $broadcastRepo;
    }

    public function mount(): void
    {
        $this->breadcrumbs = [
            ['label' => 'Dashboard', 'icon' => 'o-home', 'link' => route('dashboard')],
            ['label' => 'Notifications'],
            ['label' => 'SMS Broadcast'],
        ];
    }

    // Credits Management
    public function addCredits(): void
    {
        $this->validate([
            'credit_amount' => 'required|integer|min:1',
            'credit_description' => 'nullable|string',
        ]);

        $this->broadcastRepo->addCredits([
            'credits' => $this->credit_amount,
            'description' => $this->credit_description,
        ]);

        $this->success("Added {$this->credit_amount} SMS credits successfully");
        $this->resetCreditForm();
        $this->addCreditsModal = false;
    }

    public function getTotalCreditsProperty()
    {
        return $this->broadcastRepo->getTotalCredits();
    }

    public function getRemainingCreditsProperty()
    {
        return $this->broadcastRepo->getRemainingCredits();
    }

    public function getUsedCreditsProperty()
    {
        return $this->broadcastRepo->getUsedCredits();
    }

    public function getCreditHistoryProperty()
    {
        return $this->broadcastRepo->getCreditHistory();
    }

    // Campaign Management
    public function getRecipientsCountProperty()
    {
        // File mode — show imported count, never DB count
        if ($this->contact_source === 'file') {
            return count($this->importedcontacts);
        }

        if (! $this->filter_compliance && ! $this->filter_profession_id && ! $this->filter_registertype_id && ! $this->filter_province_id && ! $this->filter_city_id) {
            return 0;
        }

        return $this->broadcastRepo->getFilteredRecipients([
            'compliance'      => $this->filter_compliance,
            'profession_id'   => $this->filter_profession_id,
            'registertype_id' => $this->filter_registertype_id,
            'province_id'     => $this->filter_province_id,
            'city_id'         => $this->filter_city_id,
        ])->count();
    }

    public function createCampaign(): void
    {
        $this->validate([
            'campaign_name'    => 'required|string|max:255',
            'campaign_message' => 'required|string|max:160',
        ]);

        // Guard: file mode requires contacts to be loaded first
        if ($this->contact_source === 'file' && empty($this->importedcontacts)) {
            $this->error('Please upload and load your contact file before creating the campaign.');
            return;
        }

        try {
            $campaign = $this->broadcastRepo->createCampaign([
                'campaign_name'     => $this->campaign_name,
                'message'           => $this->campaign_message,
                'provider'          => $this->campaign_provider ?: config('services.sms_provider', 'esolutions'),
                'test_numbers'      => $this->test_numbers,
                'contact_source'    => $this->contact_source,
                'imported_contacts' => $this->contact_source === 'file' ? $this->importedcontacts : [],
                'filters' => [
                    'compliance'      => $this->filter_compliance,
                    'profession_id'   => $this->filter_profession_id,
                    'registertype_id' => $this->filter_registertype_id,
                    'province_id'     => $this->filter_province_id,
                    'city_id'         => $this->filter_city_id,
                ],
            ]);

            $this->success("Campaign '{$campaign->campaign_name}' created as DRAFT with {$campaign->total_recipients} recipient(s).");
            $this->resetCampaignForm();
            $this->createCampaignModal = false;
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

    public function viewCampaign($id): void
    {
        $this->selectedCampaign = $this->broadcastRepo->getCampaignById($id);
        $this->viewCampaignModal = true;
    }

    public function sendCampaign($id): void
    {
        $result = $this->broadcastRepo->sendBroadcast($id);

        if ($result['status'] === 'error') {
            $this->error($result['message']);
            return;
        }

        $this->success($result['message']);
    }

    public function deleteCampaign($id): void
    {
        $result = $this->broadcastRepo->deleteCampaign($id);
        $result['status'] === 'success'
            ? $this->success($result['message'])
            : $this->error($result['message']);
    }

    public function markSent($id): void
    {
        $campaign = \App\Models\Smsbroadcast::find($id);
        if ($campaign) {
            $sent   = $campaign->recipients()->where('status', 'SENT')->count();
            $failed = $campaign->recipients()->where('status', 'FAILED')->count();
            $campaign->update([
                'sent_count'   => $sent,
                'failed_count' => $failed,
                'credits_used' => $sent,
                'status'       => 'SENT',
            ]);
            $this->success('Campaign marked as SENT.');
        }
    }

    public function checkDeliveryStatus($id): void
    {
        $result = $this->broadcastRepo->checkDeliveryStatus($id);
        if ($result['status'] === 'success') {
            $this->success($result['message']);
            // Refresh selected campaign if viewing
            if ($this->selectedCampaign && $this->selectedCampaign->id == $id) {
                $this->selectedCampaign = $this->broadcastRepo->getCampaignById($id);
            }
        } else {
            $this->error($result['message']);
        }
    }

    public function getCampaignsProperty()
    {
        return $this->broadcastRepo->getCampaigns();
    }

    // Dropdowns
    public function getComplianceOptionsProperty()
    {
        return [
            ['id' => 'Valid', 'name' => 'Valid Certificates'],
            ['id' => 'Expired', 'name' => 'Expired Certificates'],
        ];
    }

    public function getProvidersProperty()
    {
        return [
            ['id' => 'esolutions', 'name' => 'eSolutions'],
            ['id' => 'nhume',      'name' => 'Nhume'],
        ];
    }

    public function getProfessionsProperty()
    {
        return Profession::orderBy('name')->get();
    }

    public function getRegistertypesProperty()
    {
        return Registertype::orderBy('name')->get();
    }

    public function getProvincesProperty()
    {
        return Province::orderBy('name')->get();
    }

    public function getCitiesProperty()
    {
        if ($this->filter_province_id) {
            return City::where('province_id', $this->filter_province_id)->orderBy('name')->get();
        }

        return City::orderBy('name')->get();
    }

    public function updatedFilterProvinceId(): void
    {
        $this->filter_city_id = null;
    }

    // Reset methods
    private function resetCreditForm(): void
    {
        $this->credit_amount = null;
        $this->credit_description = null;
    }

    private function resetCampaignForm(): void
    {
        $this->campaign_name       = null;
        $this->campaign_message    = null;
        $this->contact_source      = 'db';
        $this->campaign_provider   = '';
        $this->test_numbers        = '';
        $this->filter_compliance   = null;
        $this->filter_profession_id   = null;
        $this->filter_registertype_id = null;
        $this->filter_province_id  = null;
        $this->filter_city_id      = null;
        $this->importedcontacts    = [];
        $this->contactfile         = null;
    }

    // ── Contact template downloads ────────────────────────────────────────────
    public function downloadTemplate(string $type): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        if ($type === 'csv') {
            return response()->streamDownload(function () {
                $h = fopen('php://output', 'w');
                fputcsv($h, ['phone']);          // header
                fputcsv($h, ['263772000111']);   // sample row 1
                fputcsv($h, ['263771000222']);   // sample row 2
                fputcsv($h, ['263774000333']);   // sample row 3
                fclose($h);
            }, 'sms_contacts_template.csv', ['Content-Type' => 'text/csv']);
        }

        // TXT — one number per line, no trailing commas
        return response()->streamDownload(function () {
            echo "263772000111\n";
            echo "263771000222\n";
            echo "263774000333\n";
        }, 'sms_contacts_template.txt', ['Content-Type' => 'text/plain']);
    }
    public function sendTest(): void
    {
        $this->validate([
            'test_phone'   => 'required|string',
            'test_message' => 'required|string|max:160',
        ]);

        $result = $this->broadcastRepo->sendTestSms($this->test_phone, $this->test_message);

        if ($result['status'] === 'success') {
            $this->success($result['message']);
            $this->testmodal = false;
            $this->reset(['test_phone', 'test_message']);
        } else {
            $this->error($result['message']);
        }
    }

    // ── Import contacts from TXT file ─────────────────────────────────────────
    public function importContacts(): void
    {
        if (! $this->contactfile) {
            $this->error('Please select a file first.');
            return;
        }

        $ext = strtolower($this->contactfile->getClientOriginalExtension());
        if (! in_array($ext, ['txt', 'csv'])) {
            $this->error('Only .txt or .csv files are supported.');
            return;
        }

        // Read content directly from the Livewire temporary file object
        $content = file_get_contents($this->contactfile->getRealPath());

        if (empty($content)) {
            $this->error('The file appears to be empty.');
            return;
        }

        // Pass content directly to parser (skip file store/path lookup)
        $phones = $this->broadcastRepo->parsePhoneContent($content, $ext);

        if (empty($phones)) {
            $this->error('No valid phone numbers found. File content preview: ' . substr($content, 0, 100));
            return;
        }

        $this->importedcontacts = $phones;
        $this->success(count($phones) . ' contact(s) loaded successfully.');
    }

    // ── Nhume balance ─────────────────────────────────────────────────────────
    public function getNhumeBalanceProperty()
    {
        if (config('services.sms_provider') !== 'nhume') {
            return null;
        }
        return $this->broadcastRepo->getNhumeBalance();
    }

    public function getProviderProperty(): string
    {
        return config('services.sms_provider', 'esolutions');
    }

    public function render()
    {
        return view('livewire.admin.notifications.smsbroadcast-management', [
            'campaigns'      => $this->campaigns,
            'totalCredits'   => $this->totalCredits,
            'usedCredits'    => $this->usedCredits,
            'remainingCredits' => $this->remainingCredits,
            'creditHistory'  => $this->creditHistory,
            'recipientsCount' => $this->recipientsCount,
            'professions'    => $this->professions,
            'registertypes'  => $this->registertypes,
            'provinces'      => $this->provinces,
            'cities'         => $this->cities,
            'complianceOptions' => $this->complianceOptions,
            'nhumeBalance'   => $this->nhumeBalance,
            'provider'       => $this->provider,
            'providers'      => $this->providers,
        ]);
    }
}




