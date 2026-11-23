<?php

namespace App\Services\Whatsapp;

use App\Interfaces\invoiceInterface;
use App\Interfaces\isuspenseInterface;
use App\Models\Customer;
use App\Models\Customerapplication;
use App\Models\Customerprofession;
use App\Models\Customerregistration;
use App\Models\Invoice;
use App\Models\WhatsappLink;
use App\Models\WhatsappSession;
use App\Notifications\WhatsappLinkCodeNotification;
use App\Services\CertificateService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * The WhatsApp conversation. A number is "unlinked" until the practitioner proves they own
 * a record (registration number + a code emailed to the address on file). After that they get
 * a menu: registration status, certificates, wallet and invoices, CPD points.
 */
class WhatsappBot
{
    private const GREETINGS = ['hi', 'hello', 'hey', 'start', 'menu', 'm_menu', '0', 'help me'];

    private const CODE_MINUTES = 10;

    private const MAX_CODE_TRIES = 5;

    private const MAX_CODE_REQUESTS_PER_HOUR = 5;

    public function __construct(
        private readonly WhatsappClient $wa,
        private readonly CertificateService $certificates,
        private readonly isuspenseInterface $suspense,
        private readonly invoiceInterface $invoices,
        private readonly Flows\RenewalFlow $renewal,
        private readonly Flows\PaymentFlow $payments,
        private readonly Flows\ProfileFlow $profile,
        private readonly Flows\ProfessionFlow $registration,
    ) {}

    /**
     * @param  array{from:string,id?:string,type?:string,text?:string,reply_id?:string}  $msg
     */
    public function handle(array $msg): void
    {
        $phone = preg_replace('/\D/', '', $msg['from']);
        if ($phone === '') {
            return;
        }

        $session = WhatsappSession::firstOrCreate(['phone' => $phone]);
        $session->last_message_at = now();

        $input = isset($msg['reply_id']) && $msg['reply_id'] !== ''
            ? (string) $msg['reply_id']
            : strtolower(trim((string) ($msg['text'] ?? '')));

        try {
            $link = WhatsappLink::with('customer')->where('phone', $phone)->first();
            if ($link && ! $link->customer) {
                $link->delete(); // the customer record is gone
                $link = null;
            }

            if ($link) {
                // The portal's logic records who did things, so act as this practitioner's own web account
                $user = $link->customer->customeruser?->user;
                if ($user) {
                    Auth::setUser($user);
                }
                $this->handleLinked($phone, $link->customer, $session, $input, $msg, (bool) $user);
            } else {
                $this->handleUnlinked($phone, $session, $input, (string) ($msg['text'] ?? ''), (string) ($msg['type'] ?? 'text'), $msg);
            }
        } catch (\Throwable $e) {
            Log::error('WhatsApp bot error: '.$e->getMessage(), ['phone' => $phone, 'trace' => $e->getTraceAsString()]);
            $this->wa->sendText($phone, 'Sorry, something went wrong on our side. Please try again in a moment, or send *menu*.');
        }

        $session->save();
    }

    // ------------------------------------------------------------------
    // Not linked yet
    // ------------------------------------------------------------------

    private function handleUnlinked(string $phone, WhatsappSession $session, string $input, string $text, string $type, array $msg = []): void
    {
        if (in_array($input, self::GREETINGS, true) || $input === 'cancel') {
            $this->resetSession($session);
            $this->wa->sendButtons($phone, "Welcome to the MLCSCZ practitioner assistant.\n\nAre you already registered with the Council?", [
                ['id' => 'wa_have', 'title' => 'I have a reg number'],
                ['id' => 'wa_new', 'title' => 'New registration'],
            ]);

            return;
        }

        if ($input === 'wa_have') {
            $this->resetSession($session);
            $session->state = 'await_reg';
            $this->wa->sendText($phone, "Please send your *registration number* (for example 254564). We will email you a code to confirm it is you.");

            return;
        }

        if ($input === 'wa_new') {
            $this->profile->start($phone, $session);

            return;
        }

        if ($session->state === 'new') {
            $this->profile->handle($phone, $session, $input, $text, $msg);

            return;
        }

        if ($session->state === 'await_code') {
            $this->verifyCode($phone, $session, $input);

            return;
        }

        if ($type !== 'text' || $text === '') {
            $this->wa->sendText($phone, 'Please type your registration number as a message. Send *hi* to start again.');

            return;
        }

        $this->startLink($phone, $session, $text);
    }

    private function startLink(string $phone, WhatsappSession $session, string $text): void
    {
        $reg = strtoupper(preg_replace('/\s+/', '', trim($text)));
        if (strlen($reg) < 3 || strlen($reg) > 30 || ! preg_match('/^[A-Z0-9\-\/]+$/', $reg)) {
            $this->wa->sendText($phone, 'That does not look like a registration number. Please send just the number, for example *254564*.');

            return;
        }

        // Limit how many codes one phone can ask for, so this cannot be used to spam someone's email.
        $key = 'wa_code_requests:'.$phone;
        Cache::add($key, 0, now()->addHour());
        if (Cache::increment($key) > self::MAX_CODE_REQUESTS_PER_HOUR) {
            $this->wa->sendText($phone, 'Too many attempts. Please try again in an hour, or contact MLCSCZ.');

            return;
        }

        $customer = $this->findCustomer($reg);
        if (! $customer) {
            $this->wa->sendText($phone, 'We could not find that registration number. Please check it and send it again, or send *hi* to start over.');

            return;
        }

        if (blank($customer->email)) {
            $this->wa->sendText($phone, 'We have no email address on file for that record, so we cannot verify you here. Please contact MLCSCZ to update your details.');

            return;
        }

        // One code request per customer per hour as well
        $ckey = 'wa_code_customer:'.$customer->id;
        Cache::add($ckey, 0, now()->addHour());
        if (Cache::increment($ckey) > self::MAX_CODE_REQUESTS_PER_HOUR) {
            $this->wa->sendText($phone, 'A code was requested too many times for this record. Please try again in an hour.');

            return;
        }

        $code = (string) random_int(100000, 999999);
        $session->state = 'await_code';
        $session->code_hash = $this->hashCode($code);
        $session->code_expires_at = now()->addMinutes(self::CODE_MINUTES);
        $session->attempts = 0;
        $session->data = ['customer_id' => $customer->id];

        $customer->notify(new WhatsappLinkCodeNotification($code));

        $this->wa->sendText($phone, "Thank you. We have emailed a 6-digit code to *{$this->maskEmail($customer->email)}*.\n\nReply here with the code (it expires in ".self::CODE_MINUTES." minutes). Send *cancel* to stop.");
    }

    private function verifyCode(string $phone, WhatsappSession $session, string $input): void
    {
        $customerid = $session->data['customer_id'] ?? null;

        if (! $customerid || ! $session->code_hash || ! $session->code_expires_at || $session->code_expires_at->isPast()) {
            $this->resetSession($session);
            $session->state = 'await_reg';
            $this->wa->sendText($phone, 'That code has expired. Please send your registration number again to get a new one.');

            return;
        }

        $session->attempts = (int) $session->attempts + 1;

        if (! hash_equals($session->code_hash, $this->hashCode(preg_replace('/\D/', '', $input)))) {
            if ($session->attempts >= self::MAX_CODE_TRIES) {
                $this->resetSession($session);
                $this->wa->sendText($phone, 'Too many wrong codes, so we have stopped. Send *hi* to start again.');
            } else {
                $this->wa->sendText($phone, 'That code is not right. Please check your email and try again, or send *cancel*.');
            }

            return;
        }

        $customer = Customer::find($customerid);
        if (! $customer) {
            $this->resetSession($session);
            $this->wa->sendText($phone, 'We could not complete the link. Please contact MLCSCZ.');

            return;
        }

        WhatsappLink::updateOrCreate(['phone' => $phone], ['customer_id' => $customer->id, 'linked_at' => now()]);
        $this->resetSession($session);

        $this->wa->sendText($phone, 'Done! This WhatsApp number is now linked to *'.$this->fullName($customer).'*.');
        $this->sendMenu($phone, $customer);
    }

    // ------------------------------------------------------------------
    // Linked: the menu
    // ------------------------------------------------------------------

    private function handleLinked(string $phone, Customer $customer, WhatsappSession $session, string $input, array $msg, bool $hasAccount): void
    {
        // Saying hi/menu/cancel always leaves whatever step the user was in
        if (in_array($input, self::GREETINGS, true) || $input === 'cancel' || $input === 'unlink_no') {
            $this->resetSession($session);
            $this->sendMenu($phone, $customer);

            return;
        }

        // Renewal and payment need the practitioner's web account (the portal records who acted)
        $needsAccount = in_array($input, ['m_renew', 'renew', '6', 'm_apps', 'applications', '7', 'm_register', 'register', '8'], true)
            || str_starts_with($input, 'app_') || str_starts_with($input, 'ren_') || str_starts_with($input, 'pay_')
            || str_starts_with($input, 'reg_') || str_starts_with($input, 'rg')
            || in_array($session->state, ['ren', 'pay', 'apps', 'reg'], true);
        if ($needsAccount && ! $hasAccount) {
            $this->resetSession($session);
            $this->wa->sendText($phone, 'Your record has no portal login yet, so renewals and payments cannot be done here. Please contact MLCSCZ.');

            return;
        }

        // A step in progress (renewal, payment) gets the reply first
        if ($session->state === 'ren') {
            $this->renewal->handle($phone, $customer, $session, $input, $msg);
            $this->afterPayment($phone, $customer, $session);

            return;
        }
        if ($session->state === 'reg') {
            $this->registration->handle($phone, $customer, $session, $input, $msg);

            return;
        }
        if ($session->state === 'pay') {
            $this->payments->handle($phone, $customer, $session, $input, $msg);
            $this->afterPayment($phone, $customer, $session);

            return;
        }

        $this->resetSession($session);

        if (str_starts_with($input, 'app_')) {
            $this->renewal->openApplication($phone, $customer, $session, (int) substr($input, 4));

            return;
        }
        if (str_starts_with($input, 'reg_cp_')) {
            $cp = \App\Models\Customerprofession::where('customer_id', $customer->id)->find((int) substr($input, 7));
            if ($cp) {
                $this->registration->continueProfession($phone, $customer, $session, $cp);
            } else {
                $this->wa->sendText($phone, 'That registration could not be found. Send *applications* to see the list.');
            }

            return;
        }
        if (str_starts_with($input, 'cr_')) {
            $this->sendRegistrationCertificate($phone, $customer, (int) substr($input, 3));

            return;
        }
        if (str_starts_with($input, 'pc_')) {
            $this->sendPractisingCertificate($phone, $customer, (int) substr($input, 3));

            return;
        }

        switch ($input) {
            case 'm_status': case 'status': case '1':
                $this->sendStatus($phone, $customer);
                break;
            case 'm_certs': case 'certificate': case 'certificates': case '2':
                $this->sendCertificateList($phone, $customer);
                break;
            case 'm_money': case 'balance': case 'wallet': case 'invoice': case 'invoices': case '3':
                $this->sendMoney($phone, $customer);
                break;
            case 'm_cpd': case 'cpd': case 'points': case '4':
                $this->sendCpd($phone, $customer);
                break;
            case 'm_help': case 'help': case '5':
                $this->sendHelp($phone);
                break;
            case 'm_renew': case 'renew': case '6':
                $this->renewal->start($phone, $customer, $session);
                break;
            case 'm_apps': case 'applications': case 'application': case '7':
                $this->renewal->listApplications($phone, $customer, $session);
                break;
            case 'm_register': case 'register': case '8':
                $this->registration->start($phone, $customer, $session);
                break;
            case 'm_unlink': case 'unlink':
                $this->wa->sendButtons($phone, 'Unlink this WhatsApp number from your record? You can link it again any time.', [
                    ['id' => 'unlink_yes', 'title' => 'Yes, unlink'],
                    ['id' => 'unlink_no', 'title' => 'No, keep it'],
                ]);
                break;
            case 'unlink_yes':
                WhatsappLink::where('phone', $phone)->delete();
                $this->wa->sendText($phone, 'This number has been unlinked. Send *hi* any time to link it again.');
                break;
            default:
                $this->wa->sendText($phone, ! empty($msg['media'])
                    ? 'Thanks for the file, but I was not expecting one. Choose an option from the menu first.'
                    : "Sorry, I didn't understand that. Choose an option from the menu.");
                $this->sendMenu($phone, $customer);
        }
    }

    /** After an invoice is paid from the wallet, move on to whatever comes next for that application. */
    private function afterPayment(string $phone, Customer $customer, WhatsappSession $session): void
    {
        $invoice = $this->payments->paid;
        $this->payments->paid = null;
        if (! $invoice) {
            return;
        }

        if ($invoice->source === 'customerprofession') {
            $cp = \App\Models\Customerprofession::find($invoice->source_id);
            if ($cp) {
                $this->registration->continueProfession($phone, $customer, $session, $cp);
            }

            return;
        }

        $this->wa->sendText($phone, "Your application now goes to MLCSCZ for approval. We will message you here when there is an update. Send *applications* to follow it.");
    }

    private function sendMenu(string $phone, Customer $customer): void
    {
        $this->wa->sendList($phone, 'Hello '.trim($customer->name).'. What would you like to do?', 'Open menu', [
            ['id' => 'm_status', 'title' => 'My registration', 'description' => 'Registration and practising status'],
            ['id' => 'm_certs', 'title' => 'My certificates', 'description' => 'Download your certificates as PDF'],
            ['id' => 'm_money', 'title' => 'Wallet & invoices', 'description' => 'Balance and what you owe'],
            ['id' => 'm_renew', 'title' => 'Renew certificate', 'description' => 'Renew or maintain your registration'],
            ['id' => 'm_register', 'title' => 'Register a profession', 'description' => 'Start a new registration'],
            ['id' => 'm_apps', 'title' => 'My applications', 'description' => 'Check or continue an application'],
            ['id' => 'm_cpd', 'title' => 'CPD points', 'description' => 'Your points against the minimum'],
            ['id' => 'm_help', 'title' => 'Help', 'description' => 'What I can do'],
            ['id' => 'm_unlink', 'title' => 'Unlink this number', 'description' => 'Remove the link to your record'],
        ], 'MLCSCZ');
    }

    private function sendStatus(string $phone, Customer $customer): void
    {
        $professions = $this->professions($customer);
        if ($professions->isEmpty()) {
            $this->wa->sendText($phone, "You have no professions on your record yet. Visit the portal to register: ".config('generalutils.base_url'));

            return;
        }

        $lines = ['*Your registration*'];
        foreach ($professions as $cp) {
            $registration = $cp->registration;
            $lines[] = '';
            $lines[] = '*'.($cp->profession->name ?? 'Profession').'*'.($cp->registrationnumber ? ' ('.$cp->registrationnumber.')' : '');
            $lines[] = 'Registration: '.($registration ? $registration->status.($registration->certificatenumber ? ' - '.$registration->certificatenumber : '') : 'not registered');

            $approved = $cp->applications->where('status', 'APPROVED')->sortByDesc('year')->first();
            if ($approved) {
                $lines[] = 'Practising certificate: '.($approved->isValid() ? 'valid until '.$this->date($approved->certificate_expiry_date) : 'EXPIRED on '.$this->date($approved->certificate_expiry_date));
            } else {
                $lines[] = 'Practising certificate: none yet';
            }

            $latest = $cp->applications->sortByDesc('id')->first();
            if ($latest && $latest->status !== 'APPROVED') {
                $lines[] = 'Latest application: '.$latest->year.' - '.$latest->status;
            }
        }
        $lines[] = '';
        $lines[] = 'Send *menu* for more options.';

        $this->wa->sendText($phone, implode("\n", $lines));
    }

    private function sendCertificateList(string $phone, Customer $customer): void
    {
        $rows = [];
        foreach ($this->professions($customer) as $cp) {
            $name = $cp->profession->name ?? 'Profession';

            if ($cp->registration && $cp->registration->status === 'APPROVED') {
                $rows[] = ['id' => 'cr_'.$cp->registration->id, 'title' => 'Registration cert', 'description' => $name.' '.$cp->registration->certificatenumber];
            }
            foreach ($cp->applications->where('status', 'APPROVED')->sortByDesc('year') as $application) {
                if ($application->isValid()) {
                    $rows[] = ['id' => 'pc_'.$application->id, 'title' => 'Practising '.$application->year, 'description' => $name.' - expires '.$this->date($application->certificate_expiry_date)];
                }
            }
        }

        if (empty($rows)) {
            $this->wa->sendText($phone, "You have no certificates available to download yet.\n\nA registration certificate appears once your registration is approved, and a practising certificate once your renewal is approved and paid.");

            return;
        }

        $this->wa->sendList($phone, 'Choose the certificate you want. It will be sent here as a PDF.', 'Choose certificate', $rows, 'Certificates');
    }

    private function sendRegistrationCertificate(string $phone, Customer $customer, int $registrationId): void
    {
        $registration = Customerregistration::where('id', $registrationId)->where('status', 'APPROVED')
            ->whereHas('customerprofession', fn ($q) => $q->where('customer_id', $customer->id))->first();

        if (! $registration) {
            $this->wa->sendText($phone, 'That certificate is not available. Send *certificates* to see the list.');

            return;
        }

        $this->wa->sendText($phone, 'Preparing your registration certificate, one moment...');
        $pdf = $this->certificates->registrationPdf($registration);
        $ok = $this->wa->sendDocument($phone, $pdf, 'registration_certificate_'.$registration->certificatenumber.'.pdf', 'Registration certificate '.$registration->certificatenumber);

        if (! $ok) {
            $this->wa->sendText($phone, 'Sorry, we could not send the file right now. You can also download it from the portal: '.config('generalutils.base_url'));
        }
    }

    private function sendPractisingCertificate(string $phone, Customer $customer, int $applicationId): void
    {
        $application = Customerapplication::where('id', $applicationId)->where('status', 'APPROVED')
            ->whereHas('customerprofession', fn ($q) => $q->where('customer_id', $customer->id))->first();

        if (! $application || ! $application->isValid()) {
            $this->wa->sendText($phone, 'That certificate is not available (it may have expired). Send *certificates* to see the list.');

            return;
        }

        $this->wa->sendText($phone, 'Preparing your practising certificate, one moment...');
        $pdf = $this->certificates->practisingPdf($application);
        $ok = $this->wa->sendDocument($phone, $pdf, 'practising_certificate_'.$application->year.'.pdf', 'Practising certificate '.$application->year);

        if (! $ok) {
            $this->wa->sendText($phone, 'Sorry, we could not send the file right now. You can also download it from the portal: '.config('generalutils.base_url'));
        }
    }

    private function sendMoney(string $phone, Customer $customer): void
    {
        $lines = ['*Wallet*'];
        $wallet = collect($this->suspense->getbalances($customer->id))->filter(fn ($b) => (float) $b['balance'] > 0);
        if ($wallet->isEmpty()) {
            $lines[] = 'No money in your wallet.';
        } else {
            foreach ($wallet as $b) {
                $lines[] = $b['currency'].' '.number_format((float) $b['balance'], 2);
            }
        }

        $lines[] = '';
        $lines[] = '*Unpaid invoices*';
        $unpaid = Invoice::with('currency')->where('customer_id', $customer->id)->whereIn('status', ['PENDING', 'AWAITING'])->orderByDesc('id')->limit(8)->get();
        if ($unpaid->isEmpty()) {
            $lines[] = 'You have no unpaid invoices.';
        } else {
            foreach ($unpaid as $invoice) {
                $balance = (float) $this->invoices->getinvoicebalance($invoice->id, $invoice->currency_id);
                $lines[] = $invoice->invoice_number.' - '.($invoice->description ?: 'Invoice').' - '.($invoice->currency->name ?? '').' '.number_format($balance, 2).($invoice->status === 'AWAITING' ? ' (awaiting verification)' : '');
            }
            $lines[] = '';
            $lines[] = 'When you pay at the bank, use the *invoice number* (or your registration number) as the payment reference so it is matched quickly.';
        }

        $lines[] = '';
        $lines[] = 'Pay or view invoices on the portal: '.config('generalutils.base_url');

        $this->wa->sendText($phone, implode("\n", $lines));
    }

    private function sendCpd(string $phone, Customer $customer): void
    {
        $professions = $this->professions($customer)->filter(fn ($cp) => $cp->status === 'APPROVED');
        if ($professions->isEmpty()) {
            $this->wa->sendText($phone, 'You have no approved profession on your record, so there are no CPD points to show yet.');

            return;
        }

        $lines = ['*CPD points*'];
        foreach ($professions as $cp) {
            $year = $cp->applications->sortByDesc('id')->first()?->year;
            $points = (float) $cp->totalcdpoints($year);
            $minimum = (float) ($cp->profession?->tires?->where('tire_id', $cp->tire_id)?->first()?->minimum_cdp ?? 0);
            $lines[] = '';
            $lines[] = '*'.($cp->profession->name ?? 'Profession').'*'.($year ? " ({$year})" : '');
            $lines[] = 'Points: '.rtrim(rtrim(number_format($points, 2), '0'), '.').' of '.rtrim(rtrim(number_format($minimum, 2), '0'), '.').' required '.($points >= $minimum ? '- requirement met' : '- not yet met');
        }
        $lines[] = '';
        $lines[] = 'Record activities on the CPD platform or the portal to add points.';

        $this->wa->sendText($phone, implode("\n", $lines));
    }

    private function sendHelp(string $phone): void
    {
        $this->wa->sendText($phone, "*Help*\n\nSend *menu* to see your options, or type:\n- *register* - register for a profession\n- *renew* - renew or maintain your certificate\n- *applications* - check or continue an application\n- *status* - registration status\n- *certificates* - download certificates\n- *balance* - wallet and invoices\n- *cpd* - CPD points\n- *unlink* - remove this number\n- *menu* - stop what you are doing and go back to the menu\n\nStudent registration and some other services are on the portal: ".config('generalutils.base_url'));
    }

    // ------------------------------------------------------------------
    // helpers
    // ------------------------------------------------------------------

    private function professions(Customer $customer)
    {
        return Customerprofession::with(['profession.tires', 'registration', 'applications'])
            ->where('customer_id', $customer->id)->orderBy('id')->get();
    }

    private function findCustomer(string $reg): ?Customer
    {
        $profession = Customerprofession::where('registrationnumber', $reg)->first();
        if ($profession && $profession->customer) {
            return $profession->customer;
        }

        return Customer::where('regnumber', $reg)->first();
    }

    private function resetSession(WhatsappSession $session): void
    {
        $session->state = null;
        $session->data = null;
        $session->code_hash = null;
        $session->code_expires_at = null;
        $session->attempts = 0;
    }

    private function hashCode(string $code): string
    {
        return hash_hmac('sha256', $code, (string) config('app.key'));
    }

    private function maskEmail(string $email): string
    {
        [$user, $domain] = array_pad(explode('@', $email, 2), 2, '');

        return substr($user, 0, 1).str_repeat('*', max(strlen($user) - 1, 2)).'@'.$domain;
    }

    private function fullName(Customer $customer): string
    {
        return trim($customer->name.' '.$customer->surname);
    }

    private function date($value): string
    {
        try {
            return $value ? Carbon::parse($value)->format('d M Y') : 'n/a';
        } catch (\Throwable $e) {
            return (string) $value;
        }
    }
}
