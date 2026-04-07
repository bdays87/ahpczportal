<?php

namespace App\Livewire;

use App\Interfaces\iotherapplicationInterface;
use App\Interfaces\iprovinceInterface;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class Registeredinstitutions extends Component
{
    use WithPagination;

    public $search = '';
    public $servicefilter = '';
    public $province_id = '';
    public $practitioner = '';
    public $selectedinstitution = null;
    public $detailmodal = false;

    protected $repo;
    protected $provincerepo;

    public function boot(iotherapplicationInterface $repo, iprovinceInterface $provincerepo)
    {
        $this->repo = $repo;
        $this->provincerepo = $provincerepo;
    }

    public function updatedSearch()       { $this->resetPage(); }
    public function updatedServicefilter(){ $this->resetPage(); }
    public function updatedProvinceId()   { $this->resetPage(); }
    public function updatedPractitioner() { $this->resetPage(); }

    public function viewdetail($id)
    {
        $this->selectedinstitution = $this->repo->getbyid($id);
        // eager load for modal
        $this->selectedinstitution->load(
            'customer.province',
            'otherservice',
            'instservices',
            'instcustomers.customer.customerprofessions.registertype',
            'instcustomers.customer.customerprofessions.applications'
        );
        $this->detailmodal = true;
    }

    public function clearfilters()
    {
        $this->reset('search', 'servicefilter', 'province_id', 'practitioner');
        $this->resetPage();
    }

    #[Layout('components.layouts.plain')]
    public function render()
    {
        return view('livewire.registeredinstitutions', [
            'institutions'   => $this->repo->getvalidinstitutions($this->search, $this->servicefilter, $this->province_id, $this->practitioner),
            'serviceoptions' => $this->repo->getserviceoptions(),
            'provinces'      => $this->provincerepo->getAll(),
        ]);
    }
}
