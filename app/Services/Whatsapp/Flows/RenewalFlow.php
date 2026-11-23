<?php

namespace App\Services\Whatsapp\Flows;

use App\Interfaces\icustomerapplicationInterface;
use App\Interfaces\icustomerprofessionInterface;
use App\Interfaces\iapplicationsessionInterface;
use App\Models\Applicationtype;
use App\Models\Customer;
use App\Models\Customerapplication;
use App\Models\Customerprofession;
use App\Models\WhatsappSession;
use App\Services\Whatsapp\WhatsappClient;

/**
 * Renew (or maintain) a practising certificate from WhatsApp, step for step like the portal:
 * pick the profession and renewal period, choose the renewal type, upload the required documents,
 * pay the invoice, and follow the application until it is approved.
 */
class RenewalFlow
{
    use FlowHelpers;

    public function __construct(
        private readonly WhatsappClient $wa,
        private readonly icustomerprofessionInterface $professions,
        private readonly icustomerapplicationInterface $applications,
        private readonly iapplicationsessionInterface $sessions,
        private readonly PaymentFlow $payments,
    ) {}

    // ------------------------------------------------------------------
    // Start
    // ------------------------------------------------------------------

    public function start(string $phone, Customer $customer, WhatsappSession $session): void
    {
        $eligible = Customerprofession::with('profession')->where('customer_id', $customer->id)->where('status', 'APPROVED')->orderBy('id')->get();

        if ($eligible->isEmpty()) {
            $this->wa->sendText($phone, 'You do not have an approved profession to renew yet. Send *applications* to see where your application is.');

            return;
        }

        $session->state = 'ren';

        if ($eligible->count() === 1) {
            $this->chooseProfession($phone, $customer, $session, $eligible->first());

            return;
        }

        $session->data = ['step' => 'prof'];
        $this->wa->sendList($phone, 'Which profession do you want to renew?', 'Choose', $eligible->map(fn ($cp) => [
            'id' => 'ren_p_'.$cp->id,
            'title' => mb_substr($cp->profession->name ?? 'Profession', 0, 24),
            'description' => $cp->registrationnumber,
        ])->values()->all());
    }

    // ------------------------------------------------------------------
    // Replies while a renewal is in progress
    // ------------------------------------------------------------------

    public function handle(string $phone, Customer $customer, WhatsappSession $session, string $input, array $msg): void
    {
        $step = $session->data['step'] ?? 'prof';

        if (str_starts_with($input, 'pay_')) {
            // The invoice offer is shown from this flow, so its buttons come back here
            $session->state = 'pay';
            $this->payments->handle($phone, $customer, $session, $input, $msg);

            return;
        }

        switch ($step) {
            case 'prof':
                $cp = Customerprofession::with('profession')->where('customer_id', $customer->id)->where('status', 'APPROVED')
                    ->find((int) str_replace('ren_p_', '', $input));
                if ($cp) {
                    $this->chooseProfession($phone, $customer, $session, $cp);
                } else {
                    $this->start($phone, $customer, $session);
                }
                break;

            case 'year':
                if (str_starts_with($input, 'ren_y_')) {
                    $this->chooseYear($phone, $customer, $session, (int) substr($input, 6));
                } else {
                    $this->chooseProfession($phone, $customer, $session, Customerprofession::with('profession')->find($session->data['cp_id']));
                }
                break;

            case 'type':
                if (str_starts_with($input, 'ren_t_')) {
                    $this->createApplication($phone, $customer, $session, (int) substr($input, 6));
                } else {
                    $this->askType($phone, $session);
                }
                break;

            case 'doc':
                $this->receiveDocument($phone, $customer, $session, $msg);
                break;

            case 'resubmit':
                if ($input === 'ren_resubmit') {
                    $this->resubmit($phone, $session);
                } else {
                    $this->continueApplication($phone, $customer, $session, $session->data['uuid']);
                }
                break;

            default:
                $this->start($phone, $customer, $session);
        }
    }

    // ------------------------------------------------------------------
    // Steps
    // ------------------------------------------------------------------

    private function chooseProfession(string $phone, Customer $customer, WhatsappSession $session, Customerprofession $cp): void
    {
        $open = $this->sessions->getopenrenewalsessions()->values();

        if ($open->isEmpty()) {
            $this->reset($session);
            $this->wa->sendText($phone, 'Renewal is not open right now. Please try again when the renewal period opens. Send *menu* for other options.');

            return;
        }

        $session->state = 'ren';
        $session->data = ['step' => 'year', 'cp_id' => $cp->id];

        if ($open->count() === 1) {
            $this->chooseYear($phone, $customer, $session, (int) $open->first()->year);

            return;
        }

        $this->wa->sendList($phone, 'Which renewal period?', 'Choose period', $open->map(fn ($s) => ['id' => 'ren_y_'.$s->year, 'title' => (string) $s->year])->all());
    }

    private function chooseYear(string $phone, Customer $customer, WhatsappSession $session, int $year): void
    {
        $cp = Customerprofession::with('profession', 'applications')->find($session->data['cp_id']);
        $open = $this->sessions->getopenrenewalsessions();
        if (! $cp || ! $open->contains(fn ($s) => (int) $s->year === $year)) {
            $this->reset($session);
            $this->wa->sendText($phone, 'That renewal period is not open. Send *renew* to start again.');

            return;
        }

        $existing = Customerapplication::where('customerprofession_id', $cp->id)->where('year', $year)->first();
        if ($existing) {
            if ($existing->status === 'APPROVED') {
                $this->reset($session);
                $this->wa->sendText($phone, "Your {$year} renewal is already approved. Send *certificates* to download your practising certificate.");

                return;
            }

            // An application for this period was already started: carry on with it
            $data = $session->data;
            $data['year'] = $year;
            $session->data = $data;
            $this->continueApplication($phone, $customer, $session, $existing->uuid);

            return;
        }

        if ($cp->applications->isEmpty()) {
            $this->reset($session);
            $this->wa->sendText($phone, 'We could not find a previous practising application for this profession, so it cannot be renewed here. Please contact MLCSCZ.');

            return;
        }

        $data = $session->data;
        $data['step'] = 'type';
        $data['year'] = $year;
        $session->data = $data;
        $this->askType($phone, $session);
    }

    private function askType(string $phone, WhatsappSession $session): void
    {
        $types = Applicationtype::where('name', '!=', 'NEW')->get();

        if ($types->count() <= 3) {
            $this->wa->sendButtons($phone, "Choose the type of application for {$session->data['year']}:", $types->map(fn ($t) => [
                'id' => 'ren_t_'.$t->id, 'title' => $this->typeLabel($t->name),
            ])->all());

            return;
        }

        $this->wa->sendList($phone, "Choose the type of application for {$session->data['year']}:", 'Choose type', $types->map(fn ($t) => [
            'id' => 'ren_t_'.$t->id, 'title' => $this->typeLabel($t->name), 'description' => $t->description ?? null,
        ])->all());
    }

    private function createApplication(string $phone, Customer $customer, WhatsappSession $session, int $typeId): void
    {
        $type = Applicationtype::find($typeId);
        if (! $type || $type->name === 'NEW') {
            $this->askType($phone, $session);

            return;
        }

        $response = $this->professions->renew($session->data['cp_id'], ['year' => $session->data['year'], 'applicationtype_id' => $type->id]);

        if (($response['status'] ?? '') !== 'success') {
            $this->reset($session);
            $this->wa->sendText($phone, 'We could not start the renewal: '.($response['message'] ?? 'unknown error'));

            return;
        }

        $application = Customerapplication::find($response['data']);
        $this->wa->sendText($phone, "Your {$session->data['year']} ".strtolower($this->typeLabel($type->name)).' application has been started.');
        $this->continueApplication($phone, $customer, $session, $application->uuid);
    }

    /** Work out what the application still needs: documents, then payment, then (if rejected) resubmitting. */
    public function continueApplication(string $phone, Customer $customer, WhatsappSession $session, string $uuid): void
    {
        $payload = $this->professions->getapplicationbyuuid($uuid);
        if (empty($payload['data'])) {
            $this->reset($session);
            $this->wa->sendText($phone, 'We could not find that application.');

            return;
        }

        $application = $payload['data'];
        $documents = collect($payload['uploaddocuments']);
        $missing = $documents->where('upload', false)->values();

        $session->state = 'ren';
        $session->data = array_merge($session->data ?? [], ['uuid' => $uuid, 'cp_id' => $application->customerprofession_id]);

        if ($application->status === 'REJECTED' && empty($session->data['shown_rejection'])) {
            $this->wa->sendText($phone, '*Your application was rejected.*'."\n".($application->comment ?: 'Please check your documents, replace any that are wrong, then resubmit.'));
            $session->data = array_merge($session->data, ['shown_rejection' => true]);
        }

        if ($missing->isNotEmpty()) {
            $next = $missing->first();
            $session->data = array_merge($session->data, ['step' => 'doc', 'doc_id' => $next['document_id']]);
            $number = $documents->count() - $missing->count() + 1;
            $this->wa->sendText($phone, "*Document {$number} of {$documents->count()}*\nPlease send your *{$next['document_name']}* as a PDF or a clear photo (max 10 MB).\n\nSend *menu* to stop; your progress is saved and you can continue later from *applications*.");

            return;
        }

        if ($application->status === 'REJECTED') {
            $session->data = array_merge($session->data, ['step' => 'resubmit']);
            $this->wa->sendButtons($phone, 'All documents are uploaded. Resubmit your application for review?', [['id' => 'ren_resubmit', 'title' => 'Resubmit now']]);

            return;
        }

        $invoice = $payload['invoice'];
        if (! $invoice) {
            $this->reset($session);
            $this->wa->sendText($phone, 'Your documents are in. We could not find the invoice for this application, so please contact MLCSCZ.');

            return;
        }

        $this->wa->sendText($phone, 'All documents received.');
        $this->payments->offer($phone, $customer, $session, $invoice);
    }

    private function receiveDocument(string $phone, Customer $customer, WhatsappSession $session, array $msg): void
    {
        if (empty($msg['media'])) {
            $this->wa->sendText($phone, 'Please send the document as a PDF or a photo, or send *menu* to stop and continue later.');

            return;
        }

        $application = Customerapplication::where('uuid', $session->data['uuid'])->first();
        if (! $application) {
            $this->reset($session);

            return;
        }

        $path = $this->storeMedia($this->wa, $msg['media']);
        if ($this->isError($path)) {
            $this->wa->sendText($phone, $this->errorText($path));

            return;
        }

        $response = $this->professions->uploadrenewaldocuments([
            'document_id' => $session->data['doc_id'],
            'status' => 'PENDING',
            'file' => $path,
            'customerapplication_id' => $application->id,
        ]);

        if (($response['status'] ?? '') !== 'success') {
            $this->wa->sendText($phone, 'We could not save that document: '.($response['message'] ?? 'unknown error').'. Please send it again.');

            return;
        }

        $this->wa->sendText($phone, 'Received.');
        $this->continueApplication($phone, $customer, $session, $application->uuid);
    }

    private function resubmit(string $phone, WhatsappSession $session): void
    {
        $response = $this->applications->resubmit($session->data['uuid']);
        $this->reset($session);

        $this->wa->sendText($phone, ($response['status'] ?? '') === 'success'
            ? 'Your application has been resubmitted for review. We will message you here when there is an update.'
            : 'We could not resubmit: '.($response['message'] ?? 'unknown error'));
    }

    // ------------------------------------------------------------------
    // My applications
    // ------------------------------------------------------------------

    public function listApplications(string $phone, Customer $customer, WhatsappSession $session): void
    {
        $apps = Customerapplication::with('customerprofession.profession')
            ->whereHas('customerprofession', fn ($q) => $q->where('customer_id', $customer->id))
            ->orderByDesc('id')->limit(6)->get();

        // Registrations that are not finished or approved yet
        $registrations = Customerprofession::with('profession')->where('customer_id', $customer->id)
            ->whereNotIn('status', ['APPROVED', 'REJECTED'])->orderByDesc('id')->limit(3)->get();

        if ($apps->isEmpty() && $registrations->isEmpty()) {
            $this->wa->sendText($phone, 'You have no applications yet. Send *renew* to start a renewal, or *register* to start a new registration.');

            return;
        }

        $types = Applicationtype::pluck('name', 'id');
        $session->state = 'apps';
        $session->data = null;

        $rows = $registrations->map(fn ($cp) => [
            'id' => 'reg_cp_'.$cp->id,
            'title' => 'New registration',
            'description' => mb_substr(($cp->profession->name ?? '').' - '.$cp->status, 0, 72),
        ])->concat($apps->map(fn ($a) => [
            'id' => 'app_'.$a->id,
            'title' => mb_substr($a->year.' '.$this->typeLabel($types[$a->applicationtype_id] ?? 'Application'), 0, 24),
            'description' => mb_substr(($a->customerprofession->profession->name ?? '').' - '.$a->status, 0, 72),
        ]))->values()->all();

        $this->wa->sendList($phone, 'Your applications. Choose one to see where it is or to continue it.', 'Choose', $rows);
    }

    public function openApplication(string $phone, Customer $customer, WhatsappSession $session, int $applicationId): void
    {
        $application = Customerapplication::whereHas('customerprofession', fn ($q) => $q->where('customer_id', $customer->id))->find($applicationId);
        if (! $application) {
            $this->wa->sendText($phone, 'That application could not be found. Send *applications* to see the list.');

            return;
        }

        if ($application->status === 'APPROVED') {
            $this->reset($session);
            $this->wa->sendText($phone, "Your {$application->year} application is *approved*. Send *certificates* to download your practising certificate.");

            return;
        }

        $this->continueApplication($phone, $customer, $session, $application->uuid);
    }

    private function typeLabel(string $name): string
    {
        return $name === 'MAINTANCE' ? 'Maintenance' : ucfirst(strtolower($name));
    }

    private function reset(WhatsappSession $session): void
    {
        $session->state = null;
        $session->data = null;
    }
}
