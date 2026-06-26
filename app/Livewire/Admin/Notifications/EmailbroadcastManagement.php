<?php

namespace App\Livewire\Admin\Notifications;

use App\Interfaces\iemailbroadcastInterface;
use App\Jobs\SendEmailCampaignJob;
use App\Models\City;
use App\Models\Profession;
use App\Models\Province;
use App\Models\Registertype;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
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

    // Email service provider: 'default' (SendGrid/SMTP) or 'nhume'
    public $campaign_provider = 'default';

    public $nhumeCredits = null;

    public $nhumeError = null;

    // CSV of extra recipient emails (not necessarily customers in the system)
    public $recipient_csv;

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

    public function emailProviderOptions(): array
    {
        return [
            ['id' => 'default', 'name' => 'Default (SendGrid / SMTP)'],
            ['id' => 'nhume', 'name' => 'Nhume'],
        ];
    }

    public function updatedCampaignProvider($value): void
    {
        if ($value === 'nhume') {
            $this->refreshNhumeCredits();
        }
    }

    public function refreshNhumeCredits(): void
    {
        $this->nhumeCredits = null;
        $this->nhumeError = null;
        try {
            $nhume = app(\App\Services\Nhume::class);
            if (! $nhume->configured()) {
                $this->nhumeError = 'Nhume is not configured (set NHUME_API_KEY and NHUME_FROM).';

                return;
            }
            $this->nhumeCredits = $nhume->remainingCredits('TRANSACTIONAL');
        } catch (\Throwable $e) {
            $this->nhumeError = 'Could not read Nhume credits: '.$e->getMessage();
        }
    }

    /** Extract valid email addresses from an uploaded CSV (any column, header ignored). */
    private function parseCsvEmails($file): array
    {
        $emails = [];
        $handle = fopen($file->getRealPath(), 'r');
        if ($handle === false) {
            return $emails;
        }
        while (($row = fgetcsv($handle, 0, ',')) !== false) {
            foreach ($row as $cell) {
                $cell = trim((string) $cell);
                if ($cell !== '' && filter_var($cell, FILTER_VALIDATE_EMAIL)) {
                    $emails[] = $cell;
                }
            }
        }
        fclose($handle);

        return array_values(array_unique($emails));
    }

    public function createCampaign(): void
    {
        $this->validate([
            'campaign_name' => 'required|string|max:255',
            'campaign_subject' => 'required|string|max:255',
            'campaign_message' => 'required|string',
            'campaign_provider' => 'required|in:default,nhume',
            'uploaded_files.*' => 'nullable|file|mimes:pdf,ppt,pptx,doc,docx,png,jpg,jpeg|max:5120',
            'recipient_csv' => 'nullable|file|mimes:csv,txt|max:10240',
        ], [
            'uploaded_files.*.mimes' => 'Allowed attachment types: pdf, ppt, pptx, doc, docx, png, jpg.',
        ]);

        // Upload attachments to the public disk so the sender can read them locally.
        $attachmentPaths = [];
        if ($this->uploaded_files) {
            foreach ($this->uploaded_files as $file) {
                $attachmentPaths[] = $file->store('email-attachments', 'public');
            }
        }

        // Parse extra recipients from CSV (if provided).
        $csvEmails = $this->recipient_csv ? $this->parseCsvEmails($this->recipient_csv) : [];

        $hasFilter = $this->filter_compliance || $this->filter_profession_id || $this->filter_registertype_id || $this->filter_province_id || $this->filter_city_id;
        if (! $hasFilter && empty($csvEmails)) {
            $this->error('Add at least one recipient — choose a filter or upload a CSV of emails.');

            return;
        }

        if ($this->campaign_provider === 'nhume' && ! empty($attachmentPaths)) {
            $this->warning('Note: Nhume does not support attachments — they will be ignored for this provider.');
        }

        $campaign = $this->broadcastRepo->createCampaign([
            'campaign_name' => $this->campaign_name,
            'subject' => $this->campaign_subject,
            'message' => $this->campaign_message,
            'provider' => $this->campaign_provider,
            'filters' => [
                'compliance' => $this->filter_compliance,
                'profession_id' => $this->filter_profession_id,
                'registertype_id' => $this->filter_registertype_id,
                'province_id' => $this->filter_province_id,
                'city_id' => $this->filter_city_id,
            ],
            'attachments' => $attachmentPaths,
            'recipient_emails' => $csvEmails,
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
        $campaign = $this->broadcastRepo->getCampaignById($id);
        if (! $campaign) {
            $this->error('Campaign not found');

            return;
        }

        // Queue the send so the page never hangs; mark SENDING so it isn't
        // re-queued, then process it via the worker or the "Process emails now" button.
        $campaign->update(['status' => 'SENDING']);
        SendEmailCampaignJob::dispatch($id);

        $this->success('Campaign queued for sending. Click "Process emails now" to deliver them.');
    }

    /** Number of queued jobs waiting to be processed. */
    public function pendingJobs(): int
    {
        try {
            return DB::table('jobs')->count();
        } catch (\Throwable $e) {
            return 0;
        }
    }

    /**
     * Drain the queue from the browser so campaigns actually go out even when
     * no background worker is running.
     */
    public function processQueue(): void
    {
        try {
            @set_time_limit(0);
            $before = $this->pendingJobs();
            Artisan::call('app:send-pending-emails', ['--max-time' => 30]);
            $after = $this->pendingJobs();
            $processed = max(0, $before - $after);

            if ($after > 0) {
                $this->warning("Processed {$processed} job(s). {$after} still pending — click again to continue.");
            } else {
                $this->success($processed > 0 ? "Processed {$processed} job(s). Queue is empty." : 'Queue is already empty.');
            }
        } catch (\Throwable $e) {
            $this->error('Could not process the queue: '.$e->getMessage());
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
        $this->campaign_provider = 'default';
        $this->uploaded_files = [];
        $this->recipient_csv = null;
        $this->nhumeCredits = null;
        $this->nhumeError = null;
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
            'emailProviderOptions' => $this->emailProviderOptions(),
        ]);
    }
}




