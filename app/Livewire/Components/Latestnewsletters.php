<?php

namespace App\Livewire\Components;

use App\Interfaces\inewsletterInterface;
use Livewire\Component;

class Latestnewsletters extends Component
{
    public $limit = 5;

    protected $newsletterRepo;

    public function boot(inewsletterInterface $newsletterRepo)
    {
        $this->newsletterRepo = $newsletterRepo;
    }

    public function getLatestNewsletters()
    {
        return $this->newsletterRepo->getLatest($this->limit);
    }

    public function render()
    {
        return view('livewire.components.latestnewsletters', [
            'newsletters' => $this->getLatestNewsletters(),
        ]);
    }
}





