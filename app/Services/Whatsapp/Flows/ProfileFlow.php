<?php

namespace App\Services\Whatsapp\Flows;

use App\Interfaces\icustomerInterface;
use App\Models\City;
use App\Models\Customer;
use App\Models\Nationality;
use App\Models\Province;
use App\Models\User;
use App\Models\WhatsappLink;
use App\Models\WhatsappSession;
use App\Notifications\WhatsappLinkCodeNotification;
use App\Services\Whatsapp\WhatsappClient;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;

/**
 * A new practitioner creates their portal account from WhatsApp: the same personal details the web form asks for,
 * with the email address verified by a code. The portal emails them their login, and this number is linked
 * to the new record.
 */
class ProfileFlow
{
    use FlowHelpers;

    private const ZIMBABWE = 230;

    public function __construct(
        private readonly WhatsappClient $wa,
        private readonly icustomerInterface $customers,
        private readonly ProfessionFlow $professions,
    ) {}

    public function start(string $phone, WhatsappSession $session): void
    {
        $session->state = 'new';
        $session->data = ['step' => 'name'];
        $this->wa->sendText($phone, "Let's create your MLCSCZ account. I will ask for your details one at a time. Send *menu* at any time to stop.\n\nWhat is your *first name(s)* as on your ID?");
    }

    public function handle(string $phone, WhatsappSession $session, string $input, string $text, array $msg)
    {
        $d = $session->data ?? ['step' => 'name'];
        $step = $d['step'];
        $value = trim($text);

        switch ($step) {
            case 'name':
                if (mb_strlen($value) < 2) { return $this->again($phone, 'Please type your first name(s).'); }
                $d['name'] = mb_convert_case($value, MB_CASE_TITLE);
                $d['step'] = 'surname';
                $this->save($session, $d);
                $this->wa->sendText($phone, 'And your *surname*?');
                break;

            case 'surname':
                if (mb_strlen($value) < 2) { return $this->again($phone, 'Please type your surname.'); }
                $d['surname'] = mb_convert_case($value, MB_CASE_TITLE);
                $d['step'] = 'dob';
                $this->save($session, $d);
                $this->wa->sendText($phone, 'Your *date of birth*? Type it like 25/03/1990.');
                break;

            case 'dob':
                $dob = $this->parseDate($value);
                if (! $dob) { return $this->again($phone, 'I could not read that date. Please type it like 25/03/1990.'); }
                $age = $dob->age;
                if ($age < 16 || $age > 100) { return $this->again($phone, 'Please check the date, it does not look right. Type it like 25/03/1990.'); }
                $d['dob'] = $dob->format('Y-m-d');
                $d['step'] = 'gender';
                $this->save($session, $d);
                $this->wa->sendButtons($phone, 'Your gender?', [['id' => 'g_MALE', 'title' => 'Male'], ['id' => 'g_FEMALE', 'title' => 'Female']]);
                break;

            case 'gender':
                if (! in_array($input, ['g_MALE', 'g_FEMALE'], true)) { return $this->again($phone, 'Please tap Male or Female.'); }
                $d['gender'] = substr($input, 2);
                $d['step'] = 'marital';
                $this->save($session, $d);
                $this->wa->sendList($phone, 'Your marital status?', 'Choose', [
                    ['id' => 'm_SINGLE', 'title' => 'Single'], ['id' => 'm_MARRIED', 'title' => 'Married'],
                    ['id' => 'm_DIVORCED', 'title' => 'Divorced'], ['id' => 'm_WIDOWED', 'title' => 'Widowed'],
                ]);
                break;

            case 'marital':
                if (! in_array($input, ['m_SINGLE', 'm_MARRIED', 'm_DIVORCED', 'm_WIDOWED'], true)) { return $this->again($phone, 'Please choose from the list.'); }
                $d['maritalstatus'] = substr($input, 2);
                $d['step'] = 'nat';
                $this->save($session, $d);
                $this->wa->sendButtons($phone, 'Your nationality?', [['id' => 'n_230', 'title' => 'Zimbabwean'], ['id' => 'n_other', 'title' => 'Another country']]);
                break;

            case 'nat':
                if ($input === 'n_230') {
                    $this->setNationality($phone, $session, $d, self::ZIMBABWE);
                } elseif ($input === 'n_other') {
                    $d['step'] = 'natsearch';
                    $this->save($session, $d);
                    $this->wa->sendText($phone, 'Type the name of your country (for example *Malawi*).');
                } else {
                    $this->again($phone, 'Please tap one of the buttons.');
                }
                break;

            case 'natsearch':
                if (str_starts_with($input, 'n_') && is_numeric(substr($input, 2))) {
                    $this->setNationality($phone, $session, $d, (int) substr($input, 2));
                    break;
                }
                $found = Nationality::where('name', 'like', '%'.$value.'%')->orderBy('name')->limit(9)->get();
                if ($found->isEmpty()) { return $this->again($phone, 'I could not find that country. Try typing just the first few letters.'); }
                $this->wa->sendList($phone, 'Choose your country:', 'Choose', $found->map(fn ($n) => ['id' => 'n_'.$n->id, 'title' => $n->name])->all());
                break;

            case 'idtype':
                if (! in_array($input, ['t_NATIONAL_ID', 't_PASSPORT'], true)) { return $this->again($phone, 'Please tap one of the buttons.'); }
                $d['identificationtype'] = substr($input, 2);
                $d['step'] = 'idnumber';
                $this->save($session, $d);
                $this->wa->sendText($phone, $d['identificationtype'] === 'NATIONAL_ID' && $d['nationality_id'] == self::ZIMBABWE
                    ? 'Your *national ID number*, in the format 00000000L00 (for example 63123456A12).'
                    : 'Your *passport / identity number*?');
                break;

            case 'idnumber':
                $idnumber = strtoupper(str_replace([' ', '-'], '', $value));
                if (strlen($idnumber) < 5) { return $this->again($phone, 'Please type your ID number.'); }
                if ($d['nationality_id'] == self::ZIMBABWE && $d['identificationtype'] === 'NATIONAL_ID' && ! preg_match('/^[0-9]{8,9}[A-Z][0-9]{2}$/', $idnumber)) {
                    return $this->again($phone, 'That does not match the required format 00000000L00 (8 or 9 digits, a letter, 2 digits). Please try again.');
                }
                if (Customer::where('identificationnumber', $idnumber)->exists()) {
                    $this->wa->sendText($phone, 'There is already a record with that ID number. If that is you, send *hi* and choose *I have a registration number* to link this phone. Otherwise contact MLCSCZ.');
                    $session->state = null;
                    $session->data = null;
                    break;
                }
                $d['identificationnumber'] = $idnumber;
                if ($d['nationality_id'] == self::ZIMBABWE) {
                    $d['step'] = 'province';
                    $this->save($session, $d);
                    $this->wa->sendList($phone, 'Which *province* do you live in?', 'Choose', Province::orderBy('name')->get()->map(fn ($p) => ['id' => 'p_'.$p->id, 'title' => $p->name])->all());
                } else {
                    $d['step'] = 'address';
                    $this->save($session, $d);
                    $this->wa->sendText($phone, 'Your *residential address*?');
                }
                break;

            case 'province':
                $province = str_starts_with($input, 'p_') ? Province::find((int) substr($input, 2)) : null;
                if (! $province) { return $this->again($phone, 'Please choose a province from the list.'); }
                $d['province_id'] = $province->id;
                $d['step'] = 'city';
                $this->save($session, $d);
                $this->sendPagedList($this->wa, $phone, 'Which *city or town*? Tap Choose, or type part of the name to search.', 'Choose', City::where('province_id', $province->id)->orderBy('name')->get()->map(fn ($c) => ['id' => 'c_'.$c->id, 'title' => $c->name])->all(), 1, 'cpg_');
                break;

            case 'city':
                // Typing part of a name searches the list (there can be many towns)
                if (! str_starts_with($input, 'c_') && ! str_starts_with($input, 'cpg_')) {
                    $found = City::where('province_id', $d['province_id'])->where('name', 'like', '%'.$value.'%')->orderBy('name')->limit(9)->get();
                    if ($found->isEmpty()) { return $this->again($phone, 'No city or town matches that. Try fewer letters.'); }
                    $this->wa->sendList($phone, 'Choose your city or town:', 'Choose', $found->map(fn ($c) => ['id' => 'c_'.$c->id, 'title' => $c->name])->all());
                    break;
                }
                if (str_starts_with($input, 'cpg_')) {
                    $this->sendPagedList($this->wa, $phone, 'Which *city or town*? Tap Choose, or type part of the name to search.', 'Choose', City::where('province_id', $d['province_id'])->orderBy('name')->get()->map(fn ($c) => ['id' => 'c_'.$c->id, 'title' => $c->name])->all(), (int) substr($input, 4), 'cpg_');
                    break;
                }
                $city = str_starts_with($input, 'c_') ? City::where('province_id', $d['province_id'])->find((int) substr($input, 2)) : null;
                if (! $city) { return $this->again($phone, 'Please choose a city from the list.'); }
                $d['city_id'] = $city->id;
                $d['step'] = 'address';
                $this->save($session, $d);
                $this->wa->sendText($phone, 'Your *residential address* (street, suburb)?');
                break;

            case 'address':
                if (mb_strlen($value) < 5) { return $this->again($phone, 'Please type your full residential address.'); }
                $d['address'] = $value;
                $d['step'] = 'pob';
                $this->save($session, $d);
                $this->wa->sendText($phone, 'Your *place of birth* (town or country)?');
                break;

            case 'pob':
                if (mb_strlen($value) < 2) { return $this->again($phone, 'Please type your place of birth.'); }
                $d['place_of_birth'] = $value;
                $d['step'] = 'email';
                $this->save($session, $d);
                $this->wa->sendText($phone, 'Your *email address*? We will send a code to confirm it, and your portal login details to it.');
                break;

            case 'email':
                $email = strtolower($value);
                if (! filter_var($email, FILTER_VALIDATE_EMAIL)) { return $this->again($phone, 'That does not look like an email address. Please type it again.'); }
                if (Customer::where('email', $email)->exists() || User::where('email', $email)->exists()) {
                    return $this->again($phone, 'That email already has an account. If it is yours, send *hi* and choose *I have a registration number*. Otherwise type a different email.');
                }
                $code = (string) random_int(100000, 999999);
                $d['email'] = $email;
                $d['step'] = 'emailcode';
                $session->code_hash = hash_hmac('sha256', $code, (string) config('app.key'));
                $session->code_expires_at = now()->addMinutes(10);
                $session->attempts = 0;
                $this->save($session, $d);
                Notification::route('mail', $email)->notify(new WhatsappLinkCodeNotification($code));
                $this->wa->sendText($phone, "We emailed a 6-digit code to *{$email}*. Type it here to confirm the address (it expires in 10 minutes).");
                break;

            case 'emailcode':
                $session->attempts = (int) $session->attempts + 1;
                $ok = $session->code_hash && $session->code_expires_at && $session->code_expires_at->isFuture()
                    && hash_equals($session->code_hash, hash_hmac('sha256', preg_replace('/\D/', '', $value), (string) config('app.key')));
                if (! $ok) {
                    if ($session->attempts >= 5 || ! $session->code_expires_at || $session->code_expires_at->isPast()) {
                        $d['step'] = 'email';
                        $session->code_hash = null;
                        $this->save($session, $d);
                        $this->wa->sendText($phone, 'That code has expired or was wrong too many times. Please type your email address again to get a new one.');
                    } else {
                        $this->wa->sendText($phone, 'That code is not right. Please check your email and try again.');
                    }
                    break;
                }
                $session->code_hash = null;
                $d['step'] = 'phone';
                $this->save($session, $d);
                $this->wa->sendButtons($phone, 'Email confirmed. Which *phone number* should we keep on your record?', [['id' => 'ph_this', 'title' => 'Use this number'], ['id' => 'ph_other', 'title' => 'Type another']]);
                break;

            case 'phone':
                if ($input === 'ph_this') {
                    $d['phone'] = '+'.$phone;
                } elseif ($input === 'ph_other') {
                    $d['step'] = 'phonetext';
                    $this->save($session, $d);
                    $this->wa->sendText($phone, 'Type the phone number, for example +263771234567.');
                    break;
                } else {
                    return $this->again($phone, 'Please tap one of the buttons.');
                }
                $this->confirm($phone, $session, $d);
                break;

            case 'phonetext':
                $digits = preg_replace('/[^\d+]/', '', $value);
                if (strlen(preg_replace('/\D/', '', $digits)) < 9) { return $this->again($phone, 'That does not look like a phone number. Please try again.'); }
                $d['phone'] = $digits;
                $this->confirm($phone, $session, $d);
                break;

            case 'confirm':
                if ($input === 'pf_yes') {
                    $this->create($phone, $session, $d);
                } elseif ($input === 'pf_no') {
                    $this->start($phone, $session);
                } else {
                    $this->again($phone, 'Please tap Create account or Start over.');
                }
                break;

            default:
                $this->start($phone, $session);
        }
    }

    // ------------------------------------------------------------------

    private function setNationality(string $phone, WhatsappSession $session, array $d, int $id): void
    {
        $d['nationality_id'] = $id;
        $d['step'] = 'idtype';
        $this->save($session, $d);
        $this->wa->sendButtons($phone, 'Which identity document will you give?', [['id' => 't_NATIONAL_ID', 'title' => 'National ID'], ['id' => 't_PASSPORT', 'title' => 'Passport']]);
    }

    private function confirm(string $phone, WhatsappSession $session, array $d): void
    {
        $d['step'] = 'confirm';
        $this->save($session, $d);
        $nationality = Nationality::find($d['nationality_id'])->name ?? '';
        $this->wa->sendText($phone, "*Please check your details*\n\nName: {$d['name']} {$d['surname']}\nDate of birth: {$d['dob']}\nGender: {$d['gender']}\nMarital status: {$d['maritalstatus']}\nNationality: {$nationality}\nID: {$d['identificationnumber']}\nAddress: {$d['address']}\nPlace of birth: {$d['place_of_birth']}\nEmail: {$d['email']}\nPhone: {$d['phone']}");
        $this->wa->sendButtons($phone, 'Is this correct?', [['id' => 'pf_yes', 'title' => 'Create account'], ['id' => 'pf_no', 'title' => 'Start over']]);
    }

    private function create(string $phone, WhatsappSession $session, array $d): void
    {
        $response = $this->customers->create([
            'name' => $d['name'],
            'surname' => $d['surname'],
            'dob' => $d['dob'],
            'gender' => $d['gender'],
            'maritalstatus' => $d['maritalstatus'],
            'nationality_id' => $d['nationality_id'],
            'province_id' => $d['province_id'] ?? null,
            'city_id' => $d['city_id'] ?? null,
            'identificationtype' => $d['identificationtype'],
            'identificationnumber' => $d['identificationnumber'],
            'address' => $d['address'],
            'place_of_birth' => $d['place_of_birth'],
            'email' => $d['email'],
            'phone' => $d['phone'],
        ]);

        $customer = ($response['status'] ?? '') === 'success' ? Customer::where('email', $d['email'])->first() : null;
        if (! $customer) {
            $session->state = null;
            $session->data = null;
            $this->wa->sendText($phone, 'We could not create your account: '.($response['message'] ?? 'unknown error').'. Send *hi* to try again, or contact MLCSCZ.');

            return;
        }

        // Same flags the web sign-up sets once the profile is filled in
        $customer->forceFill(['profile_complete' => true, 'first_login_completed' => true])->save();

        // This phone just proved it owns the email, so link it straight away
        WhatsappLink::updateOrCreate(['phone' => $phone], ['customer_id' => $customer->id, 'linked_at' => now()]);

        $this->wa->sendText($phone, "Your account is created.\n\nYour registration number is *{$customer->regnumber}*. Your portal login details were sent to {$customer->email}. This WhatsApp number is now linked to your record.");

        $this->professions->start($phone, $customer, $session, true);
    }

    private function again(string $phone, string $message)
    {
        $this->wa->sendText($phone, $message);
    }

    private function save(WhatsappSession $session, array $data): void
    {
        $session->state = 'new';
        $session->data = $data;
    }

    private function parseDate(string $value): ?Carbon
    {
        foreach (['d/m/Y', 'd-m-Y', 'd.m.Y', 'Y-m-d', 'd/m/y'] as $format) {
            try {
                $date = Carbon::createFromFormat('!'.$format, $value);
                if ($date && $date->format($format) === $value) {
                    return $date;
                }
            } catch (\Throwable $e) {
                continue;
            }
        }

        return null;
    }
}
