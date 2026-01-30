<?php

namespace App\Console\Commands;

use Devrabiul\PwaKit\Traits\PWATrait;
use Illuminate\Console\Command;

class FixPwaManifest extends Command
{
    use PWATrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pwa:fix-manifest {--force : Overwrite existing manifest.json without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix PWA manifest.json using the correct method name (workaround for package bug)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        try {
            $manifest = config('laravel-pwa-kit.manifest', []);

            if (empty($manifest)) {
                $this->error('❌ Manifest configuration is empty. Please check config/laravel-pwa-kit.php');

                return self::FAILURE;
            }

            if (empty($manifest['icons'])) {
                $this->error('⚠️ Manifest is missing required "icons". Operation aborted.');

                return self::FAILURE;
            }

            $this->line('🔄 Updating manifest.json...');

            // Use the correct method name: createOrUpdateData instead of createOrUpdate
            $updated = $this->createOrUpdateData($manifest, $this->option('force'));

            if ($updated) {
                $this->info('✅ Manifest JSON updated successfully at public/manifest.json');

                return self::SUCCESS;
            }

            $this->warn('⚠️ Manifest file was not updated.');

            return self::FAILURE;
        } catch (\Throwable $e) {
            $this->error('❌ Error while updating the manifest: '.$e->getMessage());

            return self::FAILURE;
        }
    }
}
