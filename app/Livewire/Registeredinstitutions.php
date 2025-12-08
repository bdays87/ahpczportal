<?php

namespace App\Livewire;

use Livewire\Component;
use App\Interfaces\iotherapplicationInterface;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
class Registeredinstitutions extends Component
{
    use WithPagination;
    public $search;
    protected $repo;
    public function boot(iotherapplicationInterface $repo)
    {
        $this->repo = $repo;
    }
    public function getinstitutions(){
        return $this->repo->getvalidinstitutions($this->search);
    }
    public function headers():array{
        return [
            ['key' => 'tradename', 'label' => 'Trade Name'],
            ['key' => 'practitioner', 'label' => 'Practitioner'],
            ['key' => 'period', 'label' => 'Period'],
            ['key' => 'status', 'label' => 'Status'],
            ['key' => 'action', 'label' => ''],
        ];
    }
      #[Layout('components.layouts.plain')]
    public function render()
    {
        return view('livewire.registeredinstitutions', [
            'institutions' => $this->getinstitutions(),
            'headers' => $this->headers(),
        ]);
    }
}
