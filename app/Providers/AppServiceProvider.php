<?php

namespace App\Providers;

use Dedoc\Scramble\Scramble;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Send practitioners a WhatsApp update when staff approve or reject their application or registration,
     * or verify their payment. Never lets a WhatsApp problem get in the way of the approval itself.
     */
    private function registerWhatsappUpdates(): void
    {
        $send = function (callable $build) {
            if (! config('services.whatsapp.enabled')) {
                return;
            }
            try {
                [$customer, $text] = $build();
                if ($customer && $text) {
                    app(\App\Services\Whatsapp\WhatsappNotifier::class)->notify($customer, $text);
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('WhatsApp update failed: '.$e->getMessage());
            }
        };

        \App\Models\Customerapplication::updated(function ($application) use ($send) {
            if (! $application->wasChanged('status')) {
                return;
            }
            $send(function () use ($application) {
                $application->loadMissing('customerprofession.customer', 'customerprofession.profession');
                $name = $application->customerprofession->profession->name ?? 'your profession';
                $customer = $application->customerprofession->customer;

                return match ($application->status) {
                    'APPROVED' => [$customer, "Good news: your {$application->year} application for {$name} has been *approved*. Send *certificates* to download your practising certificate."],
                    'REJECTED' => [$customer, "Your {$application->year} application for {$name} was not approved.".($application->comment ? "\nReason: {$application->comment}" : '')."\nSend *applications* to correct it and resubmit."],
                    default => [null, null],
                };
            });
        });

        \App\Models\Customerregistration::updated(function ($registration) use ($send) {
            if (! $registration->wasChanged('status')) {
                return;
            }
            $send(function () use ($registration) {
                $registration->loadMissing('customerprofession.customer', 'customerprofession.profession');
                $name = $registration->customerprofession->profession->name ?? 'your profession';

                return $registration->status === 'APPROVED'
                    ? [$registration->customerprofession->customer, "Your registration as {$name} has been *approved*. Send *certificates* to download your registration certificate.\n\nNext, pay your application fee: send *applications* and choose your new registration."]
                    : [null, null];
            });
        });

        \App\Models\Customerprofessionqualificationassessment::updated(function ($assessment) use ($send) {
            if (! $assessment->wasChanged('status') || $assessment->status !== 'APPROVED') {
                return;
            }
            $send(function () use ($assessment) {
                $assessment->loadMissing('customerprofession.customer', 'customerprofession.profession');

                return [$assessment->customerprofession->customer, 'Your qualification assessment for '.($assessment->customerprofession->profession->name ?? 'your profession')." was *approved*. You can now pay your registration fee: send *applications* and choose your new registration."];
            });
        });

        \App\Models\Invoice::updated(function ($invoice) use ($send) {
            if (! $invoice->wasChanged('status') || $invoice->status !== 'PAID' || $invoice->getOriginal('status') !== 'AWAITING') {
                return;
            }
            $send(fn () => [$invoice->customer, "Finance has verified your payment for invoice {$invoice->invoice_number}. Thank you."]);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::unguard();

        $this->registerWhatsappUpdates();

        // @fileurl($path) — resolves file URL for current disk (public or s3)
        // Usage in blade: @fileurl($model->file)  or  @fileurl($model->file, '/imgs/noimage.jpg')
        Blade::directive('fileurl', function ($expression) {
            $parts = array_map('trim', explode(',', $expression, 2));
            $path  = $parts[0];
            $fallback = $parts[1] ?? "null";
            return "<?php echo ($path) ? \\Illuminate\\Support\\Facades\\Storage::disk(config('filesystems.default'))->url($path) : $fallback; ?>";
        });

        Scramble::configure()
        ->routes(function (Route $route) {
            return Str::startsWith($route->uri, 'api/');
        });
    }
}
