<?php

namespace App\Livewire\Admin\Components;

use App\Interfaces\idocumentInterface;
use App\Interfaces\iregistertypeInterface;
use App\Interfaces\irenewalInterface;
use App\Interfaces\itireInterface;
use App\Interfaces\iapplicationtypeInterface;
use Livewire\Component;
use Mary\Traits\Toast;

class Renewaldocuments extends Component
{
    use Toast;
    
    public $tire_id;
    public $registertype_id;
    public $document_id;
    public $applicationtype_id;
    public $modal = false;
    public $breadcrumbs = [];
    public $id;

    protected $tirerepo;
    protected $documentrepo;
    protected $registertyperepo;
    protected $applicationtyperepo;
    protected $renewalrepo;

    public function boot(
        itireInterface $tireInterface,
        idocumentInterface $documentInterface,
        iregistertypeInterface $registertypeInterface,
        irenewalInterface $renewalInterface,
        iapplicationtypeInterface $applicationtypeInterface
    ) {
        $this->tirerepo = $tireInterface;
        $this->documentrepo = $documentInterface;
        $this->registertyperepo = $registertypeInterface;
        $this->renewalrepo = $renewalInterface;
        $this->applicationtyperepo = $applicationtypeInterface;
    }

    public function getTireList()
    {
        return $this->tirerepo->getAll();
    }

    public function getDocumentList()
    {
        return $this->documentrepo->getAll('');
    }

    public function getRegistertypeList()
    {
        return $this->registertyperepo->getAll();
    }

    public function renewaldocuments()
    {
        return $this->renewalrepo->getdocuments();
    }

    public function save()
    {
        $this->validate([
            'tire_id' => 'required',
            'registertype_id' => 'required',
            'document_id' => 'required',
            'applicationtype_id' => 'required',
        ]);

        if ($this->id) {
            $this->update();
        } else {
            $this->create();
        }
        $this->reset(['id', 'tire_id', 'registertype_id', 'document_id', 'applicationtype_id']);
    }

    public function create()
    {
        $response = $this->renewalrepo->createdocument([
            'tire_id' => $this->tire_id,
            'registertype_id' => $this->registertype_id,
            'document_id' => $this->document_id,
            'applicationtype_id' => $this->applicationtype_id,
        ]);

        if ($response['success']) {
            $this->success($response['message']);
            $this->modal = false;
        } else {
            $this->error($response['message']);
        }
    }

    public function update()
    {
        $response = $this->renewalrepo->updatedocument($this->id, [
            'tire_id' => $this->tire_id,
            'registertype_id' => $this->registertype_id,
            'document_id' => $this->document_id,
            'applicationtype_id' => $this->applicationtype_id,
        ]);

        if ($response['success']) {
            $this->success($response['message']);
            $this->modal = false;
        } else {
            $this->error($response['message']);
        }
    }

    public function delete($id)
    {
        $response = $this->renewalrepo->deletedocument($id);
        if ($response['success']) {
            $this->success($response['message']);
        } else {
            $this->error($response['message']);
        }
    }

    public function edit($id)
    {
        $this->id = $id;
        $record = $this->renewalrepo->getdocument($id);
        $this->tire_id = $record->tire_id;
        $this->registertype_id = $record->registertype_id;
        $this->document_id = $record->document_id;
        $this->applicationtype_id = $record->applicationtype_id;
        $this->modal = true;
    }
    public function getApplicationtypeList()
    {
        return $this->applicationtyperepo->getAll()->where('name','!=','NEW');
    }

    public function headers(): array
    {
        return [
            ['key' => 'tire.name', 'label' => 'Tire'],
            ['key' => 'registertype.name', 'label' => 'Register Type'],
            ['key' => 'document.name', 'label' => 'Document'],
            ['key' => 'applicationtype.name', 'label' => 'Type'],
            ['key' => 'action', 'label' => ''],
        ];
    }

    public function render()
    {
        return view('livewire.admin.components.renewaldocuments', [
            'headers' => $this->headers(),
            'records' => $this->renewaldocuments(),
            'tirelist' => $this->getTireList(),
            'documentlist' => $this->getDocumentList(),
            'registertypelist' => $this->getRegistertypeList(),
            'applicationtypelist' => $this->getApplicationtypeList(),
        ]);
    }
}
