<?php

namespace App\Livewire\Admin\Components;

use App\Interfaces\icustomercontactInterface;
use Livewire\Component;
use Mary\Traits\Toast;

class Contactdetails extends Component
{
    use Toast;

    public $customer;

    public $id;

    public $name;

    public $relationship;

    public $primarycontact;

    public $secondarycontact;

    public $email;

    public $modal = false;

    protected $customercontactRepository;

    public function boot(icustomercontactInterface $customercontactRepository)
    {
        $this->customercontactRepository = $customercontactRepository;
    }

    public function mount($customer = null)
    {
        $this->customer = $customer;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required',
            'relationship' => 'required',
            'primarycontact' => 'required',
        ]);
        if ($this->id) {
            $this->update();
        } else {
            $this->create();
        }
        $this->reset(['name', 'relationship', 'primarycontact', 'secondarycontact', 'email', 'id']);
    }

    public function create()
    {
        if (! $this->customer) {
            $this->error('Customer not found.');

            return;
        }

        $response = $this->customercontactRepository->create([
            'name' => $this->name,
            'relationship' => $this->relationship,
            'primaryphone' => $this->primarycontact,
            'secondaryphone' => $this->secondarycontact,
            'email' => $this->email,
            'customer_id' => $this->customer->id,
        ]);
        if ($response['status'] == 'success') {
            $this->success($response['message']);
        } else {
            $this->error($response['message']);
        }
    }

    public function update()
    {
        if (! $this->customer) {
            $this->error('Customer not found.');

            return;
        }

        $response = $this->customercontactRepository->update($this->id, [
            'name' => $this->name,
            'relationship' => $this->relationship,
            'primaryphone' => $this->primarycontact,
            'secondaryphone' => $this->secondarycontact,
            'email' => $this->email,
            'customer_id' => $this->customer->id,
        ]);
        if ($response['status'] == 'success') {
            $this->success($response['message']);
        } else {
            $this->error($response['message']);
        }
    }

    public function delete($id)
    {
        $response = $this->customercontactRepository->delete($id);
        if ($response['status'] == 'success') {
            $this->success($response['message']);
        } else {
            $this->error($response['message']);
        }
    }

    public function edit($id)
    {
        $this->id = $id;
        $payload = $this->customercontactRepository->get($id);
        $this->name = $payload->name;
        $this->relationship = $payload->relationship;
        $this->primarycontact = $payload->primaryphone;
        $this->secondarycontact = $payload->secondaryphone;
        $this->email = $payload->email;
        $this->modal = true;
    }

    public function headers(): array
    {
        return [
            ['key' => 'name', 'label' => 'Name'],
            ['key' => 'relationship', 'label' => 'Relationship'],
            ['key' => 'primaryphone', 'label' => 'Primary Contact'],
            ['key' => 'secondaryphone', 'label' => 'Secondary Contact'],
            ['key' => 'email', 'label' => 'Email'],
        ];
    }

    public function render()
    {
        return view('livewire.admin.components.contactdetails', [
            'headers' => $this->headers(),
            'rows' => $this->customer?->contactdetails ?? collect([]),
        ]);
    }
}
