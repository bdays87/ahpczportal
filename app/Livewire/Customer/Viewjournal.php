<?php

namespace App\Livewire\Customer;

use App\Interfaces\ijournalInterface;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Viewjournal extends Component
{
    public $journal;

    public $uuid;

    protected $journalRepo;

    public function boot(ijournalInterface $journalRepo)
    {
        $this->journalRepo = $journalRepo;
    }

    public function mount($id)
    {
        $this->journal = $this->journalRepo->get($id);
        if (! $this->journal) {
            abort(404, 'Journal not found');
        }

        // Redirect to external journal link
        return redirect($this->journal->link);
    }

    #[Layout('components.layouts.practitioner')]
    public function render()
    {
        return view('livewire.customer.viewjournal');
    }
}
