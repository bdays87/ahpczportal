<?php

namespace App\implementations;

use App\Interfaces\ielectionInterface;
use App\Models\Customer;
use App\Models\Election;
use App\Models\Electioncandidate;
use App\Models\Electionposition;
use App\Models\Electionvote;
use Illuminate\Support\Facades\Auth;

class _electionRepository implements ielectionInterface
{
    protected $election;

    protected $position;

    protected $candidate;

    protected $vote;

    protected $customer;

    public function __construct(
        Election $election,
        Electionposition $position,
        Electioncandidate $candidate,
        Electionvote $vote,
        Customer $customer
    ) {
        $this->election = $election;
        $this->position = $position;
        $this->candidate = $candidate;
        $this->vote = $vote;
        $this->customer = $customer;
    }

    public function getAll()
    {
        return $this->election
            ->with(['positions.candidates.customer', 'positions.candidates.votes', 'creator'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getById($id)
    {
        return $this->election
            ->with(['positions.candidates.customer', 'positions.candidates.votes'])
            ->findOrFail($id);
    }

    public function create(array $data)
    {
        $data['createdby'] = Auth::id();
        $data['updatedby'] = Auth::id(); // Set updatedby to the same user for creation
        $data['status'] = $data['status'] ?? 'DRAFT';
        $data['publish_status'] = $data['publish_status'] ?? 'DRAFT';
        $data['year'] = $data['year'] ?? date('Y');

        return $this->election->create($data);
    }

    public function update($id, array $data)
    {
        $election = $this->election->findOrFail($id);
        $data['updatedby'] = Auth::id();
        $election->update($data);

        return $election;
    }

    public function delete($id)
    {
        $election = $this->election->findOrFail($id);
        $election->delete();

        return true;
    }

    public function getCompliantPractitioners(array $filters = [])
    {
        $query = $this->customer->whereHas('customerprofessions', function ($q) {
            $q->whereHas('applications', function ($appQuery) {
                $appQuery->where('status', 'APPROVED')
                    ->where('certificate_expiry_date', '>', now());
            });
        });

        if (! empty($filters['province_id'])) {
            $query->where('province_id', $filters['province_id']);
        }

        if (! empty($filters['city_id'])) {
            $query->where('city_id', $filters['city_id']);
        }

        if (! empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%'.$filters['search'].'%')
                    ->orWhere('surname', 'like', '%'.$filters['search'].'%')
                    ->orWhere('email', 'like', '%'.$filters['search'].'%');
            });
        }

        return $query->with('province', 'city')->orderBy('name')->get();
    }

    public function checkCustomerCompliance($customerId)
    {
        $customer = $this->customer->findOrFail($customerId);

        return $customer->customerprofessions()
            ->whereHas('applications', function ($q) {
                $q->where('status', 'APPROVED')
                    ->where('certificate_expiry_date', '>', now());
            })->exists();
    }

    public function addPosition($electionId, array $data)
    {
        $election = $this->election->findOrFail($electionId);

        $data['election_id'] = $election->id;
        $data['createdby'] = Auth::id();
        $data['updatedby'] = Auth::id(); // Set updatedby to the same user for creation
        $data['status'] = $data['status'] ?? 'ACTIVE';

        return $this->position->create($data);
    }

    public function addCandidate($positionId, array $data)
    {
        try {
            $position = $this->position->findOrFail($positionId);

            // Check if customer is compliant
            if (! $this->checkCustomerCompliance($data['customer_id'])) {
                return [
                    'status' => 'error',
                    'message' => 'This practitioner is not compliant and cannot be added as a candidate',
                ];
            }

            // Check if already a candidate for this position
            $exists = $this->candidate
                ->where('electionposition_id', $positionId)
                ->where('customer_id', $data['customer_id'])
                ->exists();

            if ($exists) {
                return [
                    'status' => 'error',
                    'message' => 'This practitioner is already a candidate for this position',
                ];
            }

            $data['electionposition_id'] = $positionId;
            $data['createdby'] = Auth::id();
            $data['updatedby'] = Auth::id(); // Set updatedby to the same user for creation
            $data['status'] = $data['status'] ?? 'ACTIVE';

            // Ensure description is not null
            if (empty($data['description'])) {
                $data['description'] = 'No description provided';
            }

            $candidate = $this->candidate->create($data);

            return [
                'status' => 'success',
                'message' => 'Candidate added successfully',
                'candidate' => $candidate,
            ];
        } catch (\Illuminate\Database\QueryException $e) {
            // Handle database constraint violations gracefully
            if ($e->getCode() === '23000') { // Integrity constraint violation
                if (str_contains($e->getMessage(), 'description') && str_contains($e->getMessage(), 'cannot be null')) {
                    return [
                        'status' => 'error',
                        'message' => 'Candidate description is required. Please provide a description for the candidate.',
                    ];
                }
                if (str_contains($e->getMessage(), 'updatedby') && str_contains($e->getMessage(), 'cannot be null')) {
                    return [
                        'status' => 'error',
                        'message' => 'There was an error processing your request. Please try again.',
                    ];
                }
            }

            // Generic error message for other database errors
            return [
                'status' => 'error',
                'message' => 'There was an error adding the candidate. Please check all required fields and try again.',
            ];
        } catch (\Exception $e) {
            // Handle any other exceptions
            return [
                'status' => 'error',
                'message' => 'An unexpected error occurred. Please try again.',
            ];
        }
    }

    public function removeCandidate($candidateId)
    {
        $candidate = $this->candidate->findOrFail($candidateId);
        $candidate->delete();

        return [
            'status' => 'success',
            'message' => 'Candidate removed successfully',
        ];
    }

    public function getElectionResults($electionId)
    {
        $election = $this->election->with(['positions.candidates.customer', 'positions.candidates.votes'])
            ->findOrFail($electionId);

        $results = [];

        foreach ($election->positions as $position) {
            $positionResults = [
                'position' => $position,
                'candidates' => [],
                'total_votes' => 0,
            ];

            foreach ($position->candidates as $candidate) {
                $voteCount = $candidate->votes()->count();
                $positionResults['total_votes'] += $voteCount;
                $positionResults['candidates'][] = [
                    'candidate' => $candidate,
                    'votes' => $voteCount,
                    'percentage' => 0, // Will calculate after getting total
                ];
            }

            // Calculate percentages
            if ($positionResults['total_votes'] > 0) {
                foreach ($positionResults['candidates'] as &$candidateData) {
                    $candidateData['percentage'] = ($candidateData['votes'] / $positionResults['total_votes']) * 100;
                }
            }

            // Sort by votes descending
            usort($positionResults['candidates'], function ($a, $b) {
                return $b['votes'] - $a['votes'];
            });

            $results[] = $positionResults;
        }

        return $results;
    }

    public function hasVoted($customerId, $electionId, $positionId)
    {
        return $this->vote
            ->where('customer_id', $customerId)
            ->where('election_id', $electionId)
            ->where('electionposition_id', $positionId)
            ->exists();
    }

    public function castVote(array $data)
    {
        // Check if customer is compliant
        if (! $this->checkCustomerCompliance($data['customer_id'])) {
            return [
                'status' => 'error',
                'message' => 'You are not compliant and cannot vote',
            ];
        }

        // Check if already voted for this position
        if ($this->hasVoted($data['customer_id'], $data['election_id'], $data['electionposition_id'])) {
            return [
                'status' => 'error',
                'message' => 'You have already voted for this position',
            ];
        }

        // Check if election is running
        $election = $this->election->findOrFail($data['election_id']);
        if (! $election->isActive()) {
            return [
                'status' => 'error',
                'message' => 'This election is not currently running',
            ];
        }

        $vote = $this->vote->create($data);

        return [
            'status' => 'success',
            'message' => 'Vote cast successfully',
            'vote' => $vote,
        ];
    }
}
