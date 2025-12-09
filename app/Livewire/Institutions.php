<?php

namespace App\Livewire;

use App\Interfaces\institutionInterface;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class Institutions extends Component
{
    use WithPagination;

    public $search = null;

    protected $repo;

    public function boot(institutionInterface $repo): void
    {
        $this->repo = $repo;
    }

    public function getInstitutionsProperty()
    {
        return $this->repo->getAll($this->search);
    }

    public function headers(): array
    {
        return [
            ['key' => 'name', 'label' => 'Institution Name'],
            ['key' => 'accredited', 'label' => 'Accreditation Status'],
        ];
    }

    #[Layout('components.layouts.plain')]
    public function render()
    {
        return view('livewire.institutions', [
            'institutions' => $this->institutions,
            'headers' => $this->headers(),
        ]);
    }
}













