<?php

namespace App\Providers;

use App\Helpers\CurrencyHelper;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Blade::directive('lkr', function (string $expression) {
            return "<?php echo \App\Helpers\CurrencyHelper::format((int)({$expression})); ?>";
        });
    }
}
