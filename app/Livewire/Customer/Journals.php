<?php

namespace App\Livewire\Customer;

use App\Interfaces\ijournalInterface;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Journals extends Component
{
    public $search = '';

    protected $journalRepo;

    public function boot(ijournalInterface $journalRepo)
    {
        $this->journalRepo = $journalRepo;
    }

    public function getJournals()
    {
        return $this->journalRepo->getAll($this->search);
    }

    public function getJournal($id)
    {
        return $this->journalRepo->get($id);
    }

  
    public function render()
    {
        return view('livewire.customer.journals', [
            'journals' => $this->getJournals(),
        ]);
    }
}
