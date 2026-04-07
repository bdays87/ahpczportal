<?php

namespace App\Livewire\Admin;

use App\Interfaces\icustomerhistoricaldataInterface;
use App\Models\Customerhistoricaldatadocument;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;

class Historicaldataapprovals extends Component
{
    use Toast, WithFileUploads;

    public $breadcrumbs = [];

    protected $repo;

    public $status = 'PENDING';

    public $historicalData = null;

    public bool $viewmodal = false;

    public bool $rejectmodal = false;

    public bool $viewattachmentmodal = false;

    public bool $docmodal = false;

    public $attachment;

    public $search;

    public $rejectionReason;

    public $admindocuments = [];

    public function mount()
    {
        $this->breadcrumbs = [
            [
                'label' => 'Dashboard',
                'icon' => 'o-home',
                'link' => route('dashboard'),
            ],
            [
                'label' => 'Historical Data Approvals',
                'route' => 'historicaldataapprovals.index',
            ],
        ];
    }

    public function boot(icustomerhistoricaldataInterface $repo)
    {
        $this->repo = $repo;
    }

    public function viewAttachment($path)
    {
        try {
            // Generate a temporary signed URL for S3 files (valid for 1 hour)
            // This ensures the file is accessible even if S3 bucket is private
            // $this->attachment = Storage::disk('s3')->temporaryUrl($path, now()->addHour());
            $this->attachment = Storage::disk('public')->temporaryUrl($path, now()->addHour());
            $this->viewattachmentmodal = true;
        } catch (\Exception $e) {
            // Fallback to regular URL if temporary URL fails (for public buckets)
            try {
                // $this->attachment = Storage::disk('s3')->url($path);
                 $this->attachment = Storage::disk('public')->url($path);
                $this->viewattachmentmodal = true;
            } catch (\Exception $e2) {
                $this->error('Failed to load document: '.$e2->getMessage());
            }
        }
    }

    public function closeAttachmentModal()
    {
        $this->viewattachmentmodal = false;
        $this->attachment = null;
    }

    public function getHistoricalData()
    {
        $data = $this->repo->getAll($this->status);

        if ($this->search) {
            $data = $data->filter(function ($item) {
                return str_contains(strtolower($item->name), strtolower($this->search))
                    || str_contains(strtolower($item->surname), strtolower($this->search))
                    || str_contains(strtolower($item->identificationnumber), strtolower($this->search));
            });
        }

        return $data;
    }

    public function viewHistoricalData($id)
    {
        $this->historicalData = $this->repo->get($id);
        $this->viewmodal = true;
    }

    public function approveHistoricalData($id)
    {
        $response = $this->repo->approve($id);

        if ($response['status'] == 'success') {
            $this->success($response['message']);
            $this->viewmodal = false;
            $this->historicalData = null;
        } else {
            $this->error($response['message']);
        }
    }

    public function openRejectModal($id)
    {
        $this->historicalData = $this->repo->get($id);
        $this->rejectmodal = true;
    }

    public function rejectHistoricalData()
    {
        $this->validate([
            'rejectionReason' => 'required|min:10',
        ], [
            'rejectionReason.required' => 'Please provide a reason for rejection',
            'rejectionReason.min' => 'Rejection reason must be at least 10 characters',
        ]);

        $response = $this->repo->reject($this->historicalData->id, $this->rejectionReason);

        if ($response['status'] == 'success') {
            $this->success($response['message']);
            $this->rejectmodal = false;
            $this->viewmodal = false;
            $this->historicalData = null;
            $this->rejectionReason = null;
        } else {
            $this->error($response['message']);
        }
    }

    public function resubmitHistoricalData($id)
    {
        $response = $this->repo->resubmit($id);

        if ($response['status'] == 'success') {
            $this->success($response['message']);
            $this->viewmodal = false;
            $this->historicalData = null;
        } else {
            $this->error($response['message']);
        }
    }

    public function openDocModal($id)
    {
        $this->historicalData  = $this->repo->get($id);
        $this->admindocuments  = [];
        foreach ($this->historicalData->professions as $profession) {
            $this->admindocuments[$profession->id] = [null, null];
        }
        $this->docmodal = true;
    }

    public function deleteDocument($documentId)
    {
        $doc = Customerhistoricaldatadocument::find($documentId);
        if ($doc) {
            Storage::disk('public')->delete($doc->file);
            $doc->delete();
        }
        $this->historicalData = $this->repo->get($this->historicalData->id);
    }

    public function saveDocumentsAndResubmit()
    {
        foreach ($this->admindocuments as $professionId => $files) {
            $profession = \App\Models\Customerhistoricaldataprofession::find($professionId);
            if (! $profession) continue;

            foreach ($files as $index => $file) {
                if ($file) {
                    $path = $file->store(config('app.docs').'/historical-certificates', 'public');
                    $profession->documents()->create([
                        'file'        => $path,
                        'description' => $index === 0 ? 'Registration Certificate' : 'Practising Certificate',
                    ]);
                }
            }
        }

        $response = $this->repo->resubmit($this->historicalData->id);

        if ($response['status'] == 'success') {
            $this->success('Documents updated and submission resubmitted for review.');
            $this->docmodal       = false;
            $this->viewmodal      = false;
            $this->historicalData = null;
            $this->admindocuments = [];
        } else {
            $this->error($response['message']);
        }
    }

    public function render()
    {
        return view('livewire.admin.historicaldataapprovals', [
            'historicalDataList' => $this->getHistoricalData(),
        ]);
    }
}
