<?php

namespace App\Livewire\Admin\Components;

use App\RenewalType;
use Livewire\Component;

class Renewaltypeselector extends Component
{
    public $selectedTypes = [];
    public $typeOptions = [];

    public function mount()
    {
        $this->typeOptions = RenewalType::options();
    }

    public function toggleType($type)
    {
        if (in_array($type, $this->selectedTypes)) {
            $this->selectedTypes = array_filter($this->selectedTypes, fn($t) => $t !== $type);
        } else {
            $this->selectedTypes[] = $type;
        }
    }

    public function isSelected($type)
    {
        return in_array($type, $this->selectedTypes);
    }

    public function render()
    {
        return view('livewire.admin.components.renewaltypeselector');
    }
}
