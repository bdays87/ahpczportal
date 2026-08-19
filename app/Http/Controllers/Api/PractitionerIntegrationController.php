<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customerprofession;
use App\Notifications\PractitionerLoginCodeNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Outbound-facing "sign in with your registration number" support for the CPD
 * Platform (see App\Http\Middleware\VerifyIntegrationSignature — every route
 * here requires a valid HMAC signature from a configured partner).
 *
 * Deliberately reg-number-only, two-step (request-code / verify-code): a
 * registration number alone isn't a secret — it's printed on certificates —
 * so a code sent to the practitioner's email on file here is the actual
 * credential, not the number itself.
 */
class PractitionerIntegrationController extends Controller
{
    private const CODE_TTL_MINUTES = 10;

    /** POST /api/integration/practitioners/request-code */
    public function requestCode(Request $request)
    {
        $data = $request->validate(['registration_number' => ['required', 'string', 'max:100']]);

        $profession = $this->findByRegistrationNumber($data['registration_number']);

        if (! $profession) {
            return response()->json(['status' => 'error', 'message' => 'Registration number not found.'], 404);
        }

        $customer = $profession->customer;

        if (! $customer || blank($customer->email)) {
            return response()->json(['status' => 'error', 'message' => 'No contact email on file for this practitioner. Contact MLCSCZ directly.'], 422);
        }

        $code = (string) random_int(100000, 999999);
        Cache::put($this->cacheKey($data['registration_number']), $code, now()->addMinutes(self::CODE_TTL_MINUTES));

        $customer->notify(new PractitionerLoginCodeNotification($code));

        return response()->json([
            'status' => 'success',
            'message' => 'Code sent.',
            'data' => ['masked_email' => $this->maskEmail($customer->email)],
        ]);
    }

    /**
     * POST /api/integration/practitioners/resolve
     *
     * Reg-number-only lookup — no code, no email round trip. cpdapp's own
     * account gate (Council.is_active +, if the practitioner has personally
     * opted into 2FA, their own authenticator code) is what stands between a
     * bare registration number and a signed-in session; this endpoint's only
     * job is "does this registration number exist, and if so, whose is it."
     */
    public function resolve(Request $request)
    {
        $data = $request->validate(['registration_number' => ['required', 'string', 'max:100']]);

        $profession = $this->findByRegistrationNumber($data['registration_number']);

        if (! $profession) {
            return response()->json(['status' => 'error', 'message' => 'Registration number not found.'], 404);
        }

        $customer = $profession->customer;

        return response()->json([
            'status' => 'success',
            'message' => 'Verified.',
            'data' => [
                'council' => 'mlcscz',
                'registration_number' => $data['registration_number'],
                'name' => $customer?->name,
                'surname' => $customer?->surname,
                'email' => $customer?->email,
                'phone' => $customer?->phone,
                'profession' => $profession->profession?->name,
                'compliance_status' => $profession->getComplianceStatusAttribute(),
            ],
        ]);
    }

    /** POST /api/integration/practitioners/verify-code */
    public function verifyCode(Request $request)
    {
        $data = $request->validate([
            'registration_number' => ['required', 'string', 'max:100'],
            'code' => ['required', 'string', 'max:10'],
        ]);

        $cacheKey = $this->cacheKey($data['registration_number']);
        $expected = Cache::get($cacheKey);

        if (! $expected || ! hash_equals($expected, $data['code'])) {
            throw ValidationException::withMessages(['code' => ['That code is incorrect or has expired.']]);
        }

        Cache::forget($cacheKey);

        $profession = $this->findByRegistrationNumber($data['registration_number']);

        if (! $profession) {
            // The record could have been deleted between request-code and verify-code — rare, but handle it.
            return response()->json(['status' => 'error', 'message' => 'Registration number not found.'], 404);
        }

        $customer = $profession->customer;

        return response()->json([
            'status' => 'success',
            'message' => 'Verified.',
            'data' => [
                'council' => 'mlcscz',
                'registration_number' => $data['registration_number'],
                'name' => $customer?->name,
                'surname' => $customer?->surname,
                'email' => $customer?->email,
                'phone' => $customer?->phone,
                'profession' => $profession->profession?->name,
                'compliance_status' => $profession->getComplianceStatusAttribute(),
            ],
        ]);
    }

    private function findByRegistrationNumber(string $registrationNumber): ?Customerprofession
    {
        return Customerprofession::with('customer', 'profession')
            ->where('registrationnumber', $registrationNumber)
            ->first();
    }

    private function cacheKey(string $registrationNumber): string
    {
        return 'cpdapp_login_code:'.Str::upper(trim($registrationNumber));
    }

    private function maskEmail(string $email): string
    {
        [$local, $domain] = array_pad(explode('@', $email, 2), 2, '');
        $visible = mb_substr($local, 0, 1);

        return $visible.str_repeat('*', max(mb_strlen($local) - 1, 3)).'@'.$domain;
    }
}
