<?php

namespace App\Livewire\Admin\Notifications;

use App\Interfaces\iemailbroadcastInterface;
use App\Models\City;
use App\Models\Profession;
use App\Models\Province;
use App\Models\Registertype;
use Livewire\Component;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;

class EmailbroadcastManagement extends Component
{
    use Toast, WithFileUploads;

    protected $broadcastRepo;

    public $breadcrumbs = [];

    // Tab selection
    public $selectedTab = 'campaigns';

    // Credit properties
    public $credit_amount;

    public $credit_description;

    public $addCreditsModal = false;

    // Campaign properties
    public $campaign_name;

    public $campaign_subject;

    public $campaign_message;

    public $campaign_attachments = [];

    public $uploaded_files = [];

    // Filters
    public $filter_compliance;

    public $filter_profession_id;

    public $filter_registertype_id;

    public $filter_province_id;

    public $filter_city_id;

    // Modals
    public $createCampaignModal = false;

    public $viewCampaignModal = false;

    public $selectedCampaign = null;

    public function boot(iemailbroadcastInterface $broadcastRepo): void
    {
        $this->broadcastRepo = $broadcastRepo;
    }

    public function mount(): void
    {
        $this->breadcrumbs = [
            ['label' => 'Dashboard', 'icon' => 'o-home', 'link' => route('dashboard')],
            ['label' => 'Notifications'],
            ['label' => 'Email Broadcast'],
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

        $this->success("Added {$this->credit_amount} email credits successfully");
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
        if (! $this->filter_compliance && ! $this->filter_profession_id && ! $this->filter_registertype_id && ! $this->filter_province_id && ! $this->filter_city_id) {
            return 0;
        }

        return $this->broadcastRepo->getFilteredRecipients([
            'compliance' => $this->filter_compliance,
            'profession_id' => $this->filter_profession_id,
            'registertype_id' => $this->filter_registertype_id,
            'province_id' => $this->filter_province_id,
            'city_id' => $this->filter_city_id,
        ])->count();
    }

    public function createCampaign(): void
    {
        $this->validate([
            'campaign_name' => 'required|string|max:255',
            'campaign_subject' => 'required|string|max:255',
            'campaign_message' => 'required|string',
            'uploaded_files.*' => 'nullable|file|max:5120', // 5MB max per file
        ]);

        // Upload attachments if any
        $attachmentPaths = [];
        if ($this->uploaded_files) {
            foreach ($this->uploaded_files as $file) {
                $attachmentPaths[] = $file->store('email-attachments', 'public');
            }
        }

        $campaign = $this->broadcastRepo->createCampaign([
            'campaign_name' => $this->campaign_name,
            'subject' => $this->campaign_subject,
            'message' => $this->campaign_message,
            'filters' => [
                'compliance' => $this->filter_compliance,
                'profession_id' => $this->filter_profession_id,
                'registertype_id' => $this->filter_registertype_id,
                'province_id' => $this->filter_province_id,
                'city_id' => $this->filter_city_id,
            ],
            'attachments' => $attachmentPaths,
        ]);

        $this->success("Campaign '{$campaign->campaign_name}' created with {$campaign->total_recipients} recipients");
        $this->resetCampaignForm();
        $this->createCampaignModal = false;
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
        $this->campaign_name = null;
        $this->campaign_subject = null;
        $this->campaign_message = null;
        $this->uploaded_files = [];
        $this->filter_compliance = null;
        $this->filter_profession_id = null;
        $this->filter_registertype_id = null;
        $this->filter_province_id = null;
        $this->filter_city_id = null;
    }

    public function render()
    {
        return view('livewire.admin.notifications.emailbroadcast-management', [
            'campaigns' => $this->campaigns,
            'totalCredits' => $this->totalCredits,
            'usedCredits' => $this->usedCredits,
            'remainingCredits' => $this->remainingCredits,
            'creditHistory' => $this->creditHistory,
            'recipientsCount' => $this->recipientsCount,
            'professions' => $this->professions,
            'registertypes' => $this->registertypes,
            'provinces' => $this->provinces,
            'cities' => $this->cities,
            'complianceOptions' => $this->complianceOptions,
        ]);
    }
}




