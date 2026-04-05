<?php

namespace App\Livewire;

use Livewire\Component;
use App\Interfaces\iotherapplicationInterface;
use Illuminate\Support\Facades\Auth;
use Mary\Traits\Toast;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class Showotherapplicant extends Component
{
    use Toast, WithFileUploads;

    public $uuid;
    public $otherapplication;
    public $invoice;
    public $uploaddocuments = [];
    public $breadcrumbs = [];

    // Document upload
    public $otherservicedocument_id;
    public $document_id;
    public $uploadmodal = false;
    public $file;
    public $documenturl;
    public $documentview = false;

    // Institution service
    public $servicemodal = false;
    public $service_name;
    public $service_description;

    // Institution employee
    public $employeemodal = false;
    public $customer_search;
    public $customer_results = [];
    public $selected_customer_id;
    public $employmenttype = 'CONTRACT';
    public $date_employed;

    protected $otherapplicationrepo;

    public function boot(iotherapplicationInterface $otherapplicationrepo)
    {
        $this->otherapplicationrepo = $otherapplicationrepo;
    }

    public function mount($uuid)
    {
        $this->uuid = $uuid;
        $this->getotherapplication();
    }

    public function getotherapplication()
    {
        $payload = $this->otherapplicationrepo->getbyuuid($this->uuid);
        $this->otherapplication = $payload['data'];
        $this->invoice = $payload['invoice'];
        $this->uploaddocuments = $payload['uploaddocuments'];

        if (Auth::user()->accounttype_id == 1) {
            $this->breadcrumbs = [
                ['label' => 'Dashboard', 'icon' => 'o-home', 'link' => route('dashboard')],
                ['label' => 'Customer', 'icon' => 'o-home', 'link' => route('customers.show', $this->otherapplication->customer->uuid)],
                ['label' => 'Other Application'],
            ];
        } else {
            $this->breadcrumbs = [
                ['label' => 'Dashboard', 'icon' => 'o-home', 'link' => route('dashboard')],
                ['label' => 'Other Application'],
            ];
        }
    }

    // ── Documents ──────────────────────────────────────────────
    public function openuploaddocument($otherservicedocument_id, $document_id)
    {
        $this->otherservicedocument_id = $otherservicedocument_id;
        $this->document_id = $document_id;
        $this->uploadmodal = true;
    }

    public function uploaddocument()
    {
        $this->validate(['file' => 'required|file|mimes:pdf']);
        $path = $this->file->store('documents', 'public');
        $response = $this->otherapplicationrepo->createdocument([
            'otherapplication_id' => $this->otherapplication->id,
            'otherservicedocument_id' => $this->otherservicedocument_id,
            'file' => $path,
        ]);
        if ($response['status'] == 'success') {
            $this->uploadmodal = false;
            $this->getotherapplication();
            $this->success('Document uploaded successfully');
        } else {
            $this->error($response['message']);
        }
    }

    public function removedocument($id)
    {
        $response = $this->otherapplicationrepo->deletedocument($id);
        if ($response['status'] == 'success') {
            $this->success($response['message']);
            $this->getotherapplication();
        } else {
            $this->error($response['message']);
        }
    }

    public function viewdocument($file)
    {
        $this->documenturl = Storage::url($file);
        $this->documentview = true;
    }

    // ── Institution Services ────────────────────────────────────
    public function saveservice()
    {
        $this->validate([
            'service_name' => 'required|string|max:255',
        ]);
        $response = $this->otherapplicationrepo->addinstservice([
            'otherapplication_id' => $this->otherapplication->id,
            'name' => $this->service_name,
            'description' => $this->service_description,
        ]);
        if ($response['status'] == 'success') {
            $this->success($response['message']);
            $this->reset('service_name', 'service_description', 'servicemodal');
            $this->getotherapplication();
        } else {
            $this->error($response['message']);
        }
    }

    public function removeservice($id)
    {
        $response = $this->otherapplicationrepo->removeinstservice($id);
        if ($response['status'] == 'success') {
            $this->success($response['message']);
            $this->getotherapplication();
        } else {
            $this->error($response['message']);
        }
    }

    // ── Institution Employees ───────────────────────────────────
    public function updatedCustomerSearch()
    {
        if (strlen($this->customer_search) >= 2) {
            $this->customer_results = $this->otherapplicationrepo->searchcustomers($this->customer_search)->toArray();
        } else {
            $this->customer_results = [];
        }
    }

    public function selectcustomer($id)
    {
        $this->selected_customer_id = $id;
        $customer = collect($this->customer_results)->firstWhere('id', $id);
        $this->customer_search = $customer ? $customer['name'].' '.$customer['surname'] : '';
        $this->customer_results = [];
    }

    public function saveemployee()
    {
        $this->validate([
            'selected_customer_id' => 'required',
            'employmenttype' => 'required',
        ]);
        $response = $this->otherapplicationrepo->addinstcustomer([
            'otherapplication_id' => $this->otherapplication->id,
            'customer_id' => $this->selected_customer_id,
            'employmenttype' => $this->employmenttype,
            'date_employed' => $this->date_employed,
        ]);
        if ($response['status'] == 'success') {
            $this->success($response['message']);
            $this->reset('selected_customer_id', 'customer_search', 'employmenttype', 'date_employed', 'employeemodal');
            $this->getotherapplication();
        } else {
            $this->error($response['message']);
        }
    }

    public function removeemployee($id)
    {
        $response = $this->otherapplicationrepo->removeinstcustomer($id);
        if ($response['status'] == 'success') {
            $this->success($response['message']);
            $this->getotherapplication();
        } else {
            $this->error($response['message']);
        }
    }

    public function render()
    {
        return view('livewire.showotherapplicant');
    }
}
