<?php

namespace App\Services\Whatsapp\Flows;

use App\Interfaces\icustomerprofessionInterface;
use App\Interfaces\icustomertypeInterface;
use App\Interfaces\invoiceInterface;
use App\Interfaces\iqualificationInterface;
use App\Models\Customer;
use App\Models\Customerprofession;
use App\Models\Employmentlocation;
use App\Models\Employmentstatus;
use App\Models\Customertype;
use App\Models\Invoice;
use App\Models\Profession;
use App\Models\Qualificationcategory;
use App\Models\Qualificationlevel;
use App\Models\WhatsappSession;
use App\Services\Whatsapp\WhatsappClient;

/**
 * Register for a profession from WhatsApp, in the same order as the portal wizard:
 * choose the profession and employment details, upload the required documents, add qualifications,
 * then pay the invoices (qualification assessment, registration, application) as each one unlocks.
 */
class ProfessionFlow
{
    use FlowHelpers;

    public function __construct(
        private readonly WhatsappClient $wa,
        private readonly icustomerprofessionInterface $professions,
        private readonly icustomertypeInterface $customertypes,
        private readonly iqualificationInterface $qualifications,
        private readonly invoiceInterface $invoices,
        private readonly PaymentFlow $payments,
    ) {}

    // ------------------------------------------------------------------
    // Start: choose what to register as
    // ------------------------------------------------------------------

    public function start(string $phone, Customer $customer, WhatsappSession $session, bool $justCreated = false): void
    {
        $session->state = 'reg';
        $session->data = ['step' => 'ctype'];

        $this->wa->sendList($phone, ($justCreated ? 'Now let us register you for a profession. ' : '').'Which type of registration do you want?', 'Choose', Customertype::orderBy('id')->get()->map(fn ($t) => [
            'id' => 'rg_ct_'.$t->id, 'title' => mb_substr($t->name, 0, 24),
        ])->all());
    }

    public function handle(string $phone, Customer $customer, WhatsappSession $session, string $input, array $msg): void
    {
        $d = $session->data ?? ['step' => 'ctype'];

        switch ($d['step']) {
            case 'ctype':
                $type = str_starts_with($input, 'rg_ct_') ? Customertype::find((int) substr($input, 6)) : null;
                if (! $type) { $this->start($phone, $customer, $session); return; }
                if (strtolower($type->name) === 'student') {
                    $session->state = null;
                    $session->data = null;
                    $this->wa->sendText($phone, 'Student registration includes placement details that are only on the portal for now: '.config('generalutils.base_url'));

                    return;
                }
                $d['ctype_id'] = $type->id;
                $d['step'] = 'prof';
                $this->save($session, $d);
                $this->sendProfessions($phone, 1);
                break;

            case 'prof':
                if (str_starts_with($input, 'rgpg_')) {
                    $this->sendProfessions($phone, (int) substr($input, 5));
                    break;
                }
                $profession = str_starts_with($input, 'rg_pr_') ? Profession::find((int) substr($input, 6)) : null;
                if (! $profession) { $this->sendProfessions($phone, 1); break; }

                $existing = Customerprofession::where('customer_id', $customer->id)->where('profession_id', $profession->id)->where('status', '!=', 'REJECTED')->first();
                if ($existing) {
                    $this->wa->sendText($phone, "You already have *{$profession->name}* on your record. Let us carry on with it.");
                    $this->continueProfession($phone, $customer, $session, $existing);

                    return;
                }
                $d['profession_id'] = $profession->id;
                $registertypes = collect($this->customertypes->getregistertypes($d['ctype_id']));
                if ($registertypes->count() === 1) {
                    $d['rtype_id'] = $registertypes->first()['id'];
                    $this->askEmploymentStatus($phone, $session, $d);
                } elseif ($registertypes->isEmpty()) {
                    $d['rtype_id'] = null;
                    $this->askEmploymentStatus($phone, $session, $d);
                } else {
                    $d['step'] = 'rtype';
                    $this->save($session, $d);
                    $this->wa->sendList($phone, 'Which register?', 'Choose', $registertypes->map(fn ($r) => ['id' => 'rg_rt_'.$r['id'], 'title' => mb_substr($r['name'], 0, 24)])->all());
                }
                break;

            case 'rtype':
                if (! str_starts_with($input, 'rg_rt_')) { $this->wa->sendText($phone, 'Please choose a register from the list.'); break; }
                $d['rtype_id'] = (int) substr($input, 6);
                $this->askEmploymentStatus($phone, $session, $d);
                break;

            case 'estatus':
                if (! str_starts_with($input, 'rg_es_')) { $this->wa->sendText($phone, 'Please choose from the list.'); break; }
                $d['estatus_id'] = (int) substr($input, 6);
                $d['step'] = 'eloc';
                $this->save($session, $d);
                $this->wa->sendList($phone, 'Where do you work?', 'Choose', Employmentlocation::orderBy('id')->get()->map(fn ($l) => ['id' => 'rg_el_'.$l->id, 'title' => mb_substr($l->name, 0, 24)])->all());
                break;

            case 'eloc':
                if (! str_starts_with($input, 'rg_el_')) { $this->wa->sendText($phone, 'Please choose from the list.'); break; }
                $d['eloc_id'] = (int) substr($input, 6);
                $d['step'] = 'esector';
                $this->save($session, $d);
                $this->wa->sendButtons($phone, 'Is your employer in the public or private sector?', [['id' => 'rg_se_1', 'title' => 'Public sector'], ['id' => 'rg_se_2', 'title' => 'Private sector']]);
                break;

            case 'esector':
                if (! in_array($input, ['rg_se_1', 'rg_se_2'], true)) { $this->wa->sendText($phone, 'Please tap Public sector or Private sector.'); break; }
                $d['esector'] = (int) substr($input, 6);
                $this->createProfession($phone, $customer, $session, $d);
                break;

            case 'doc':
                $this->receiveDocument($phone, $customer, $session, $d, $msg);
                break;

            case 'qinst': case 'qqual': case 'qcat': case 'qlev': case 'qyear': case 'qfile': case 'qmore':
                $this->qualificationStep($phone, $customer, $session, $d, $input, $msg);
                break;

            default:
                $this->start($phone, $customer, $session);
        }
    }

    // ------------------------------------------------------------------
    // Creating the profession
    // ------------------------------------------------------------------

    private function askEmploymentStatus(string $phone, WhatsappSession $session, array $d): void
    {
        $d['step'] = 'estatus';
        $this->save($session, $d);
        $this->wa->sendList($phone, 'What is your employment status?', 'Choose', Employmentstatus::orderBy('id')->get()->map(fn ($s) => ['id' => 'rg_es_'.$s->id, 'title' => mb_substr($s->name, 0, 24)])->all());
    }

    private function sendProfessions(string $phone, int $page): void
    {
        $this->sendPagedList($this->wa, $phone, 'Which profession are you registering for?', 'Choose', Profession::orderBy('name')->get()->map(fn ($p) => [
            'id' => 'rg_pr_'.$p->id, 'title' => mb_substr($p->name, 0, 24), 'description' => mb_substr($p->name, 0, 72),
        ])->all(), $page, 'rgpg_');
    }

    private function createProfession(string $phone, Customer $customer, WhatsappSession $session, array $d): void
    {
        $profession = Profession::find($d['profession_id']);
        if (! $profession || blank($profession->prefix) || blank($customer->regnumber)) {
            $session->state = null;
            $session->data = null;
            $this->wa->sendText($phone, 'We could not set up that profession (missing registration number or profession prefix). Please contact MLCSCZ.');

            return;
        }

        $response = $this->professions->create([
            'customer_id' => $customer->id,
            'customertype_id' => $d['ctype_id'],
            'profession_id' => $profession->id,
            'registertype_id' => $d['rtype_id'],
            'employmentstatus_id' => $d['estatus_id'],
            'employmentlocation_id' => $d['eloc_id'],
            'employmentsector' => $d['esector'],
            'registrationnumber' => $profession->prefix.$customer->regnumber,
        ]);

        if (($response['status'] ?? '') !== 'success') {
            $session->state = null;
            $session->data = null;
            $this->wa->sendText($phone, 'We could not add that profession: '.($response['message'] ?? 'unknown error'));

            return;
        }

        $cp = Customerprofession::where('uuid', $response['data'])->first();
        $this->wa->sendText($phone, "*{$profession->name}* has been added to your record (registration number *{$cp->registrationnumber}*). Next are your documents and qualifications.");
        $this->continueProfession($phone, $customer, $session, $cp);
    }

    // ------------------------------------------------------------------
    // Carry on from wherever the registration is
    // ------------------------------------------------------------------

    public function continueProfession(string $phone, Customer $customer, WhatsappSession $session, Customerprofession $cp): void
    {
        $cp = Customerprofession::with('profession', 'qualifications')->find($cp->id);
        $session->state = 'reg';
        $session->data = array_merge(['cp_id' => $cp->id, 'uuid' => $cp->uuid, 'profession_id' => $cp->profession_id], array_intersect_key($session->data ?? [], ['ctype_id' => 1]));

        // 1. Required documents
        $payload = $this->professions->getbyuuid($cp->uuid);
        $documents = collect($payload['uploaddocuments'] ?? []);
        $missing = $documents->where('upload', false)->values();
        if ($missing->isNotEmpty()) {
            $next = $missing->first();
            $number = $documents->count() - $missing->count() + 1;
            $session->data = array_merge($session->data, ['step' => 'doc', 'doc_id' => $next['document_id']]);
            $this->wa->sendText($phone, "*Document {$number} of {$documents->count()}*\nPlease send your *{$next['document_name']}* as a PDF or a clear photo (max 10 MB).\n\nSend *menu* to stop. Your progress is saved; continue any time from *applications*.");

            return;
        }

        // 2. At least one qualification
        if ($cp->qualifications->isEmpty()) {
            $institutions = $this->qualifications->getQualificationByProfessionId($cp->profession_id);
            if ($institutions->isNotEmpty()) {
                $this->askInstitution($phone, $session, $institutions);

                return;
            }
        }

        // 3. Invoices, each unlocking the next
        $this->nextInvoice($phone, $customer, $session, $cp);
    }

    private function nextInvoice(string $phone, Customer $customer, WhatsappSession $session, Customerprofession $cp): void
    {
        $this->professions->generatepractitionerinvoice($cp->id); // only creates what does not exist yet

        $labels = ['Qualification Assessment' => 'qualification assessment', 'Registration' => 'registration', 'New Application' => 'application'];
        $anyInvoice = false;

        foreach ($labels as $type => $label) {
            $result = $this->invoices->getcustomerprofessioninvoices($cp->id, $type);
            $row = collect($result['data'] ?? [])->first(fn ($r) => str_contains($r['description'], $type));
            if (! $row) {
                continue;
            }
            $anyInvoice = true;

            if ($row['status'] === 'PAID') {
                continue;
            }
            if ($row['status'] === 'AWAITING') {
                $session->state = null;
                $session->data = null;
                $this->wa->sendText($phone, "Your {$label} fee is with finance for verification. We will message you here as soon as it is approved, and tell you what comes next.");

                return;
            }
            if ($row['button'] !== 'enabled') {
                $session->state = null;
                $session->data = null;
                $this->wa->sendText($phone, "Your {$label} fee cannot be paid yet: ".($row['comment'] ?: 'waiting for the previous step').". We will message you here when it is ready. You can also check *applications* at any time.");

                return;
            }

            $this->wa->sendText($phone, "Next is your *{$label}* fee.");
            $this->payments->offer($phone, $customer, $session, Invoice::find($row['id']));

            return;
        }

        $session->state = null;
        $session->data = null;

        if ($anyInvoice) {
            $this->wa->sendText($phone, 'All your fees are paid. Your registration is now with MLCSCZ for approval. We will message you here when there is an update.');

            return;
        }

        // No invoice yet: the registration itself is reviewed by staff first, and the application fee comes after approval
        $registration = \App\Models\Customerregistration::where('customerprofession_id', $cp->id)->first();
        if ($registration && $registration->status !== 'APPROVED') {
            $this->wa->sendText($phone, "Your documents and qualifications have been sent to MLCSCZ for review.\n\nWe will message you here when your registration is approved. After that you can pay your application fee: send *applications* and choose your registration.");

            return;
        }

        $this->wa->sendText($phone, 'We could not find an invoice for this registration. Please contact MLCSCZ.');
    }

    // ------------------------------------------------------------------
    // Documents
    // ------------------------------------------------------------------

    private function receiveDocument(string $phone, Customer $customer, WhatsappSession $session, array $d, array $msg): void
    {
        if (empty($msg['media'])) {
            $this->wa->sendText($phone, 'Please send the document as a PDF or a photo, or send *menu* to stop and continue later.');

            return;
        }

        $path = $this->storeMedia($this->wa, $msg['media'], config('app.docs').'/documents');
        if ($this->isError($path)) {
            $this->wa->sendText($phone, $this->errorText($path));

            return;
        }

        $response = $this->professions->uploadDocument([
            'document_id' => $d['doc_id'],
            'file' => $path,
            'verified' => false,
            'customerprofession_id' => $d['cp_id'],
            'applicationtype_id' => 1,
        ]);

        if (($response['status'] ?? '') !== 'success') {
            $this->wa->sendText($phone, 'We could not save that document: '.($response['message'] ?? 'unknown error').'. Please send it again.');

            return;
        }

        $this->wa->sendText($phone, 'Received.');
        $this->continueProfession($phone, $customer, $session, Customerprofession::find($d['cp_id']));
    }

    // ------------------------------------------------------------------
    // Qualifications
    // ------------------------------------------------------------------

    private function askInstitution(string $phone, WhatsappSession $session, $institutions): void
    {
        $session->data = array_merge($session->data, ['step' => 'qinst']);
        $this->wa->sendText($phone, "*Qualifications*\nAdd the qualification that makes you eligible for this profession.");
        $this->sendPagedList($this->wa, $phone, 'Which institution did you study at?', 'Choose', $institutions->map(fn ($i) => ['id' => 'rq_i_'.$i->id, 'title' => mb_substr($i->name, 0, 24), 'description' => mb_substr($i->name, 0, 72)])->all(), 1, 'rqpg_');
    }

    private function qualificationStep(string $phone, Customer $customer, WhatsappSession $session, array $d, string $input, array $msg): void
    {
        $institutions = $this->qualifications->getQualificationByProfessionId($d['profession_id']);

        switch ($d['step']) {
            case 'qinst':
                if (str_starts_with($input, 'rqpg_')) {
                    $this->sendPagedList($this->wa, $phone, 'Which institution did you study at?', 'Choose', $institutions->map(fn ($i) => ['id' => 'rq_i_'.$i->id, 'title' => mb_substr($i->name, 0, 24), 'description' => mb_substr($i->name, 0, 72)])->all(), (int) substr($input, 5), 'rqpg_');
                    break;
                }
                $institution = str_starts_with($input, 'rq_i_') ? $institutions->firstWhere('id', (int) substr($input, 5)) : null;
                if (! $institution) { $this->askInstitution($phone, $session, $institutions); break; }
                $d['step'] = 'qqual';
                $d['inst'] = $institution->id;
                $this->save($session, $d);
                $this->wa->sendList($phone, 'Which qualification?', 'Choose', $institution->qualifications->map(fn ($q) => ['id' => 'rq_q_'.$q->id, 'title' => mb_substr($q->name, 0, 24), 'description' => mb_substr($q->name, 0, 72)])->all());
                break;

            case 'qqual':
                $institution = $institutions->firstWhere('id', $d['inst']);
                $qualification = str_starts_with($input, 'rq_q_') && $institution ? $institution->qualifications->firstWhere('id', (int) substr($input, 5)) : null;
                if (! $qualification) { $this->wa->sendText($phone, 'Please choose a qualification from the list.'); break; }
                $d['step'] = 'qcat';
                $d['qual'] = $qualification->id;
                $this->save($session, $d);
                $this->wa->sendList($phone, 'Where was it obtained?', 'Choose', Qualificationcategory::orderBy('id')->get()->map(fn ($c) => ['id' => 'rq_c_'.$c->id, 'title' => mb_substr($c->name, 0, 24)])->all());
                break;

            case 'qcat':
                if (! str_starts_with($input, 'rq_c_')) { $this->wa->sendText($phone, 'Please choose from the list.'); break; }
                $d['step'] = 'qlev';
                $d['cat'] = (int) substr($input, 5);
                $this->save($session, $d);
                $this->wa->sendList($phone, 'What level is the qualification?', 'Choose', Qualificationlevel::orderBy('id')->get()->map(fn ($l) => ['id' => 'rq_l_'.$l->id, 'title' => mb_substr($l->name, 0, 24)])->all());
                break;

            case 'qlev':
                if (! str_starts_with($input, 'rq_l_')) { $this->wa->sendText($phone, 'Please choose from the list.'); break; }
                $d['step'] = 'qyear';
                $d['lev'] = (int) substr($input, 5);
                $this->save($session, $d);
                $this->wa->sendText($phone, 'In which *year* did you complete it? (for example 2015)');
                break;

            case 'qyear':
                $year = (int) trim($input);
                if ($year < 1950 || $year > (int) date('Y')) { $this->wa->sendText($phone, 'Please type a year between 1950 and '.date('Y').'.'); break; }
                $d['step'] = 'qfile';
                $d['year'] = $year;
                $this->save($session, $d);
                $this->wa->sendText($phone, 'Please send your *qualification certificate* as a PDF or a clear photo (max 10 MB).');
                break;

            case 'qfile':
                if (empty($msg['media'])) { $this->wa->sendText($phone, 'Please send the certificate as a PDF or a photo.'); break; }
                $path = $this->storeMedia($this->wa, $msg['media'], config('app.docs').'/documents');
                if ($this->isError($path)) { $this->wa->sendText($phone, $this->errorText($path)); break; }
                $response = $this->professions->addqualification([
                    'qualification_id' => $d['qual'],
                    'qualificationcategory_id' => $d['cat'],
                    'qualificationlevel_id' => $d['lev'],
                    'year' => $d['year'],
                    'file' => $path,
                    'customerprofession_id' => $d['cp_id'],
                ]);
                if (($response['status'] ?? '') !== 'success') {
                    $this->wa->sendText($phone, 'We could not save the qualification: '.($response['message'] ?? 'unknown error'));
                    break;
                }
                $d['step'] = 'qmore';
                $this->save($session, $d);
                $this->wa->sendButtons($phone, 'Qualification saved. Do you have another to add?', [['id' => 'rq_more', 'title' => 'Add another'], ['id' => 'rq_done', 'title' => 'Continue']]);
                break;

            case 'qmore':
                if ($input === 'rq_more') {
                    $this->askInstitution($phone, $session, $institutions);
                } else {
                    $this->nextInvoice($phone, $customer, $session, Customerprofession::find($d['cp_id']));
                }
                break;
        }
    }

    private function save(WhatsappSession $session, array $data): void
    {
        $session->state = 'reg';
        $session->data = $data;
    }
}
