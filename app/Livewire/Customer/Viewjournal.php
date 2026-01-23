<?php

namespace App\Livewire\Customer;

use App\Interfaces\ijournalInterface;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Viewjournal extends Component
{
    public $journal;

    public $slug;

    protected $journalRepo;

    public function boot(ijournalInterface $journalRepo)
    {
        $this->journalRepo = $journalRepo;
    }

    public function mount()
    {
        // Get slug from route parameter
        $slug = request()->route('slug');

        if (! $slug) {
            abort(404, 'Journal slug is required');
        }

        $this->slug = $slug;
        $this->journal = $this->journalRepo->getBySlug($slug);
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
