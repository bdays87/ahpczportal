<?php

namespace App\Interfaces;

interface icustomercontactreportInterface
{
    /**
     * Paginated contacts for the table view.
     */
    public function getContacts(array $filters, int $perPage = 25);

    /**
     * Full (non-paginated) collection of contacts matching the filters.
     * Used for exports and bulk sending.
     */
    public function getContactsList(array $filters);

    /**
     * Counts grouped by compliance status for the summary cards.
     */
    public function getSummary(array $filters);

    /**
     * Send an email to every contact matching the filters (with a valid email).
     */
    public function sendBulkEmail(array $filters, string $subject, string $message);

    /**
     * Send an SMS to every contact matching the filters (with a phone number).
     */
    public function sendBulkSms(array $filters, string $message);
}
