<?php

namespace App\Livewire\Admin;

use App\Interfaces\icustomerhistoricaldataInterface;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Mary\Traits\Toast;

class Historicaldataapprovals extends Component
{
    use Toast;

    public $breadcrumbs = [];

    protected $repo;

    public $status = 'PENDING';

    public $historicalData = null;

    public bool $viewmodal = false;

    public bool $rejectmodal = false;

    public bool $viewattachmentmodal = false;

    public $attachment;

    public $search;

    public $rejectionReason;

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
            $this->attachment = Storage::disk('s3')->temporaryUrl($path, now()->addHour());
            $this->viewattachmentmodal = true;
        } catch (\Exception $e) {
            // Fallback to regular URL if temporary URL fails (for public buckets)
            try {
                $this->attachment = Storage::disk('s3')->url($path);
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

    public function render()
    {
        return view('livewire.admin.historicaldataapprovals', [
            'historicalDataList' => $this->getHistoricalData(),
        ]);
    }
}
