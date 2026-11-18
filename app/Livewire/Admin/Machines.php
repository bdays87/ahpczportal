<?php

namespace App\Livewire\Admin;

use Livewire\Component;

class Machines extends Component
{
    public $breadcrumbs = [];

    public function mount()
    {
        $this->breadcrumbs = [
            ['label' => 'Dashboard', 'icon' => 'o-home', 'link' => route('dashboard')],
            ['label' => 'Machines'],
        ];
    }

    public function render()
    {
        return view('livewire.admin.machines');
    }
}
