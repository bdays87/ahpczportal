<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Customerprofession;
use App\Models\User;

/**
 * Sign in with any of: email, username, or (for practitioners) registration number.
 *  - contains "@"          -> email
 *  - matches a username    -> that user (staff)
 *  - matches a customer's registration number, or a profession's registration number -> that practitioner's login
 */
class LoginResolver
{
    public function resolve(string $identifier): ?User
    {
        $id = trim($identifier);
        if ($id === '') {
            return null;
        }

        if (str_contains($id, '@')) {
            return User::where('email', $id)->first();
        }

        $user = User::where('username', $id)->first();
        if ($user) {
            return $user;
        }

        // Practitioners: the registration number on their customer record ...
        $customer = Customer::where('regnumber', $id)->first();

        // ... or the registration number of one of their professions (e.g. CLIN SCIE527705; spaces optional)
        if (! $customer) {
            $compact = str_replace(' ', '', $id);
            $profession = Customerprofession::where('registrationnumber', $id)
                ->orWhereRaw("REPLACE(registrationnumber, ' ', '') = ?", [$compact])
                ->first();
            $customer = $profession?->customer;
        }

        return $customer?->customeruser?->user;
    }

    /**
     * Why a username cannot be used, or null if it is fine. Keeps usernames from clashing with
     * registration numbers and emails (which would make sign-in ambiguous).
     */
    public function usernameProblem(?string $username, ?int $exceptUserId = null): ?string
    {
        $username = trim((string) $username);
        if ($username === '') {
            return null;
        }
        if (! preg_match('/^[A-Za-z0-9._-]{3,50}$/', $username)) {
            return 'A username can only use letters, numbers, dots, dashes and underscores, and must be 3 to 50 characters.';
        }
        if (User::where('username', $username)->when($exceptUserId, fn ($q) => $q->where('id', '!=', $exceptUserId))->exists()) {
            return 'That username is already taken.';
        }
        $compact = str_replace(' ', '', $username);
        if (Customer::where('regnumber', $username)->exists()
            || Customerprofession::where('registrationnumber', $username)->orWhereRaw("REPLACE(registrationnumber, ' ', '') = ?", [$compact])->exists()) {
            return 'That looks like a registration number, so it cannot be used as a username.';
        }

        return null;
    }
}
