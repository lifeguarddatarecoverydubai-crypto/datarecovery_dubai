<?php

namespace App\Providers;

use App\Models\MenuItem;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        URL::forceRootUrl(config('app.url'));

        View::composer('layouts.app', function ($view) {
            $headerMenuItems = collect();

            if (Schema::hasTable('menu_items')) {
                $headerMenuItems = MenuItem::with(['activeChildren'])
                    ->header()
                    ->root()
                    ->active()
                    ->ordered()
                    ->get();
            }

            $view->with('headerMenuItems', $headerMenuItems);
        });
    }
}
