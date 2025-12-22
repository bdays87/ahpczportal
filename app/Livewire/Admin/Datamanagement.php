<?php

namespace App\Livewire\Admin;

use Livewire\Component;

class Datamanagement extends Component
{
    public $breadcrumbs = [];
    public $selectedTab = 'professionimports-tab';
    public function mount()
    {
        $this->breadcrumbs = [
            [
                'label' => 'Dashboard',
                'icon' => 'o-home',
                'link' => route('dashboard'),
            ],
            [
                'label' => 'Data Management',
            ],
        ];
    }
    public function render()
    {
        return view('livewire.admin.datamanagement');
    }
}
