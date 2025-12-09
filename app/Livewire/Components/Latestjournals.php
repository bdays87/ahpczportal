<?php

namespace App\Livewire\Components;

use App\Interfaces\ijournalInterface;
use Livewire\Component;

class Latestjournals extends Component
{
    public $limit = 5;

    protected $journalRepo;

    public function boot(ijournalInterface $journalRepo)
    {
        $this->journalRepo = $journalRepo;
    }

    public function getLatestJournals()
    {
        return $this->journalRepo->getLatest($this->limit);
    }

    public function render()
    {
        return view('livewire.components.latestjournals', [
            'journals' => $this->getLatestJournals(),
        ]);
    }
}





