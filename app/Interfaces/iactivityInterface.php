<?php

namespace App\Interfaces;

interface iactivityInterface
{
    public function create(array $data);

    public function update(int $id, array $data);

    public function delete(int $id);

    public function get(int $id);

    public function getAll();

    public function getPublished();

    public function getByProfession(int $professionId);

    public function getAvailableForCustomer(int $customerId);

    public function attachProfessions(int $activityId, array $professionIds);

    public function detachProfessions(int $activityId, array $professionIds);

    public function enroll(int $activityId, int $customerId);

    public function getEnrollments(int $activityId);

    public function getCustomerEnrollments(int $customerId);

    public function updateEnrollmentStatus(int $enrollmentId, string $status);

    public function getEnrollmentStats(int $activityId);

    public function publish(int $id);
}
