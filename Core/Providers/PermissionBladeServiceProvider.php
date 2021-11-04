<?php

namespace Modules\Core\Providers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class PermissionBladeServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function boot(Request $request)
    {

        view()->composer('*', function($view) {

            Blade::directive('isocial_access', function ($expression) {
                return "<?php if (in_array($expression,auth()->user()->getDirectPermissions()->pluck('name')->toArray())) { ?>";
            });

            Blade::directive('end_isocial_access', function () {
                return '<?php } ?>';
            });
        });


    }
}
