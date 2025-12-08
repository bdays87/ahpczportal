<?php

namespace App\Livewire\Customer;

use App\Interfaces\inewsletterInterface;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Newsletters extends Component
{
    public $search = '';

    public $selectedYear;

    protected $newsletterRepo;

    public function mount()
    {
        $this->selectedYear = date('Y');
    }

    public function boot(inewsletterInterface $newsletterRepo)
    {
        $this->newsletterRepo = $newsletterRepo;
    }

    public function getNewsletters()
    {
        return $this->newsletterRepo->getAll($this->selectedYear);
    }
    public function render()
    {
        return view('livewire.customer.newsletters', [
            'newsletters' => $this->getNewsletters(),
        ]);
    }
}
