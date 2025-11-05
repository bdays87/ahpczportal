<?php

namespace App\Interfaces;

interface ielectionInterface
{
    public function getAll();

    public function getById($id);

    public function create(array $data);

    public function update($id, array $data);

    public function delete($id);

    public function getCompliantPractitioners(array $filters = []);

    public function checkCustomerCompliance($customerId);

    public function addPosition($electionId, array $data);

    public function addCandidate($positionId, array $data);

    public function removeCandidate($candidateId);

    public function getElectionResults($electionId);

    public function hasVoted($customerId, $electionId, $positionId);

    public function castVote(array $data);
}




