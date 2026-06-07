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
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::unguard();

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
