<?php

namespace App\Livewire;

use App\Interfaces\ielectionInterface;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Mary\Traits\Toast;

class ElectionVoting extends Component
{
    use Toast;

    protected $electionRepo;

    public $selectedElection = null;

    public $votes = []; // Array to store votes: [position_id => candidate_id]

    public $breadcrumbs = [];

    public function boot(ielectionInterface $electionRepo): void
    {
        $this->electionRepo = $electionRepo;
    }

    public function mount(): void
    {
        $this->breadcrumbs = [
            ['label' => 'Dashboard', 'icon' => 'o-home', 'link' => route('dashboard')],
            ['label' => 'Elections & Voting'],
        ];

        // Check if user is compliant
        if (! $this->isCompliant()) {
            $this->warning('Your certificate has expired or you do not have an approved application. You cannot vote or view elections.');
        }
    }

    public function getActiveElectionsProperty()
    {
        return $this->electionRepo->getAll()
            ->filter(fn ($election) => $election->isPublished() &&
                ($election->isActive() || $election->isReadyToStart()));
    }

    public function selectElection($electionId): void
    {
        $this->selectedElection = $this->electionRepo->getById($electionId);
        $this->votes = [];
    }

    public function castVote($positionId, $candidateId): void
    {
        if (! $this->isCompliant()) {
            $this->error('You are not compliant and cannot vote');

            return;
        }

        $customer = Auth::user()->customer->customer;

        $result = $this->electionRepo->castVote([
            'election_id' => $this->selectedElection->id,
            'customer_id' => $customer->id,
            'electionposition_id' => $positionId,
            'electioncandidate_id' => $candidateId,
        ]);

        if ($result['status'] === 'error') {
            $this->error($result['message']);

            return;
        }

        $this->success($result['message']);
        $this->votes[$positionId] = $candidateId;
    }

    public function hasVotedForPosition($positionId): bool
    {
        if (! Auth::check() || ! Auth::user()->customer) {
            return false;
        }

        $customer = Auth::user()->customer->customer;

        return $this->electionRepo->hasVoted(
            $customer->id,
            $this->selectedElection->id,
            $positionId
        );
    }

    private function isCompliant(): bool
    {
        if (! Auth::check() || ! Auth::user()->customer) {
            return false;
        }

        $customer = Auth::user()->customer->customer;

        return $this->electionRepo->checkCustomerCompliance($customer->id);
    }

    public function render()
    {
        return view('livewire.election-voting', [
            'activeElections' => $this->activeElections,
            'isCompliant' => $this->isCompliant(),
        ]);
    }
}
