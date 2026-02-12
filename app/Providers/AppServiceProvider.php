<?php

namespace App\Providers;

use Laravel\Sanctum\Sanctum;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\PersonalAccessToken;
use App\Helpers\MenuAccessHelper;

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
        if (env('APP_ENV') == 'production') {
            URL::forceScheme('https');
        }
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);

        // Custom Blade directives for menu access
        Blade::if('canview', function ($menuId) {
            return MenuAccessHelper::canView($menuId);
        });

        Blade::if('cancreate', function ($menuId) {
            return MenuAccessHelper::canCreate($menuId);
        });

        Blade::if('canedit', function ($menuId) {
            return MenuAccessHelper::canEdit($menuId);
        });

        Blade::if('candelete', function ($menuId) {
            return MenuAccessHelper::canDelete($menuId);
        });

        Blade::if('hasrole', function ($roles) {
            return MenuAccessHelper::hasRole($roles);
        });

        Blade::if('isadmin', function () {
            return MenuAccessHelper::isAdmin();
        });

        Blade::if('issuperadmin', function () {
            return MenuAccessHelper::isSuperAdmin();
        });
    }
}
