<?php

namespace App\Livewire\Admin\Components;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Mary\Traits\Toast;
use App\Interfaces\idatamanagementInterface;
use App\Interfaces\inationalityInterface;


class Customerimports extends Component
{
    use Toast, WithFileUploads, WithPagination;

    public $file;

    public $search;

    public $id;

    public $modal = false;
    public $type;

    public $modifymodal = false;

    public $name;
    public $surname;
    public $regnumber;
    public $gender;
    public $email;
    public $nationality;
    public $province;
    public $city;

    protected $customerimportrepo;
    protected $nationalityrepo;

    public function boot(idatamanagementInterface $customerimportrepo, inationalityInterface $nationalityrepo)
    {
        $this->customerimportrepo = $customerimportrepo;
        $this->nationalityrepo = $nationalityrepo;
    }

    public function mount()
    {
        $this->breadcrumbs = [
            [
                'label' => 'Dashboard',
                'icon' => 'o-home',
                'link' => route('dashboard'),
            ],
            [
                'label' => 'Customer Imports',
            ],
        ];
    }


    public function getnationalities()
    {
        return $this->nationalityrepo->getAll(null);
    }

    public function getcustomerimports()
    {
        return $this->customerimportrepo->getallcustomers($this->search);
    }

    public function getcountries() {}

    public function saveimport()
    {
        $this->validate([
            'file' => 'required|file|mimes:csv,txt',
        ]);
        $path = $this->file->store('customerimports');
        $response = null;
        if ($this->type == 'customers') {
            $response = $this->customerimportrepo->importcustomers($path);
        } elseif ($this->type == 'users') {
            $response = $this->customerimportrepo->importusers($path);
        }
        if ($response['status'] == 'success') {
            $this->success($response['message']);
        } else {
            $this->error($response['message']);
        }
    }

    public function headers(): array
    {
        return [
            ['key' => 'id', 'label' => 'ID'],
            ['key' => 'name', 'label' => 'Name'],
            ['key' => 'surname', 'label' => 'Surname'],
            ['key' => 'regnumber', 'label' => 'RegNumber'],
            ['key' => 'gender', 'label' => 'Gender'],
            ['key' => 'email', 'label' => 'Email'],
            ['key' => 'processed', 'label' => 'Processed'],
            ['key' => 'action', 'label' => ''],
        ];
    }

    public function typelist(): array
    {
        return [
            ['id' => 'customers', 'name' => 'Customers'],
            ['id' => 'users', 'name' => 'Users'],
        ];
    }

    public function save()
    {
        $this->validate([
            'name' => 'required',
            'surname' => 'required',
            'regnumber' => 'required',
            'gender' => 'required',
            'email' => 'required'
        ]);
        if ($this->id) {
            $this->update();
        } else {
            $this->create();
        }
    }

    public function create()
    {
        $response = $this->customerimportrepo->createcustomer([
            'name' => $this->name,
            'surname' => $this->surname,
            'regnumber' => $this->regnumber,
            'gender' => $this->gender,
            'email' => $this->email
        ]);
        if ($response != null) {
            if ($response['status'] == 'success') {
                $this->success($response['message']);
            } else {
                $this->error($response['message']);
            }
            $this->reset(['name', 'surname', 'regnumber', 'gender', 'email', 'password', 'user_id']);
            $this->modifymodal = false;
        }
    }

    public function update()
    {
        $response = $this->customerimportrepo->updatecustomer($this->id, [
            'name' => $this->name,
            'surname' => $this->surname,
            'regnumber' => $this->regnumber,
            'gender' => $this->gender,
            'email' => $this->email
        ]);
        if ($response['status'] == 'success') {
            $this->success($response['message']);
        } else {
            $this->error($response['message']);
        }
        $this->reset(['name', 'surname', 'regnumber', 'gender', 'email', 'password', 'user_id']);
        $this->modifymodal = false;
    }

    public function edit($id)
    {
        $this->id = $id;
        $customerimport = $this->customerimportrepo->getcustomer($id);
        $this->name = $customerimport->name;
        $this->surname = $customerimport->surname;
        $this->regnumber = $customerimport->regnumber;
        $this->gender = $customerimport->gender;
        $this->email = $customerimport->email;
        $this->modifymodal = true;
    }
    public function delete($id)
    {
        $response = $this->customerimportrepo->deletecustomer($id);
        if ($response['status'] == 'success') {
            $this->success($response['message']);
        } else {
            $this->error($response['message']);
        }
    }
    public function render()
    {
        return view('livewire.admin.components.customerimports', ['customerimports' => $this->getcustomerimports(), 'headers' => $this->headers(), 'typelist' => $this->typelist()]);
    }
}
