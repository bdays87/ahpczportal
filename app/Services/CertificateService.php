<?php

namespace App\Services;

use App\Models\Customerapplication;
use App\Models\Customerregistration;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

/**
 * Builds certificate PDFs as raw bytes, using the same templates the admin
 * "Professional Certificates" screen uses, so the WhatsApp bot (or anything else)
 * can send them without going through a browser download.
 */
class CertificateService
{
    /** Professions that have their own registration certificate template. */
    private const REGISTRATION_TEMPLATES = [
        'RAPID HIV TESTING' => 'certificates.specialcertificates.hivregistrations',
        'STATE  CERTIFIED MEDICAL LABORATORY TECHNICIAN' => 'certificates.specialcertificates.scmltcertificate',
        'STATE  CERTIFIED BLOOD TRANSFUSION TECHNICIAN' => 'certificates.specialcertificates.scbttcertificate',
    ];

    public function registrationPdf(Customerregistration $registration): string
    {
        $registration->loadMissing('customerprofession.customer', 'customerprofession.profession');
        $customer = $registration->customerprofession->customer;
        $professionname = $registration->customerprofession->profession->name ?? '';

        $qr = $this->qr('Practitioner Name:'.$customer->name.' '.$customer->surname."\n".config('generalutils.base_url').'/verifications/registration/'.$registration->certificatenumber);

        if (config('generalutils.client') == 'AHPCZ') {
            $view = 'certificates.ahpczregistrations';
        } else {
            $view = self::REGISTRATION_TEMPLATES[$professionname] ?? 'certificates.registrations';
        }

        return Pdf::loadView($view, ['qrcode' => $qr, 'data' => $registration])->output();
    }

    public function practisingPdf(Customerapplication $application): string
    {
        $application->loadMissing('customerprofession.customer', 'customerprofession.profession', 'customerprofession.registertype');
        $customer = $application->customerprofession->customer;
        $professionname = strtoupper($application->customerprofession->profession->name ?? '');

        $qr = $this->qr('Practitioner Name:'.$customer->name.' '.$customer->surname."\n expire date:".$application->certificate_expiry_date."\n verification link:".config('generalutils.base_url').'/verifications/practising/'.$application->certificate_number);

        if ($professionname === 'RAPID HIV TESTING') {
            $view = 'certificates.specialcertificates.hivpractising';
        } elseif (config('generalutils.client') == 'AHPCZ') {
            $view = 'certificates.ahpczpractising';
        } else {
            $view = 'certificates.practising';
        }

        return Pdf::loadView($view, ['qrcode' => $qr, 'data' => $application])->output();
    }

    private function qr(string $text): string
    {
        return 'data:image/svg+xml;base64,'.base64_encode(QrCode::size(300)->format('svg')->generate($text));
    }
}
