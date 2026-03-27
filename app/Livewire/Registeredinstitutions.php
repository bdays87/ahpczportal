<?php

namespace App\Livewire;

use App\Interfaces\iotherapplicationInterface;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class Registeredinstitutions extends Component
{
    use WithPagination;

    public $search = '';
    public $servicefilter = '';
    public $selectedinstitution = null;
    public $detailmodal = false;

    protected $repo;

    public function boot(iotherapplicationInterface $repo)
    {
        $this->repo = $repo;
    }

    public function updatedSearch()    { $this->resetPage(); }
    public function updatedServicefilter() { $this->resetPage(); }

    public function viewdetail($id)
    {
        $this->selectedinstitution = $this->repo->getbyid($id);
        $this->detailmodal = true;
    }

    #[Layout('components.layouts.plain')]
    public function render()
    {
        return view('livewire.registeredinstitutions', [
            'institutions'   => $this->repo->getvalidinstitutions($this->search, $this->servicefilter),
            'serviceoptions' => $this->repo->getserviceoptions(),
        ]);
    }
}
