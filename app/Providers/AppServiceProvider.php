<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Pagination\Paginator;
use App\Models\PpdbApplication;
use App\Enums\PpdbStatus;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Global: pagination bootstrap
        Paginator::useBootstrapFive();

        // Inject badge count ke layout admin
        View::composer('components.page.admin', function ($view) {
            $ppdbCount = PpdbApplication::query()
                ->where('status', PpdbStatus::SUBMITTED) // "masuk pendaftaran"
                ->count();

            $view->with('ppdbCount', $ppdbCount);
        });
    }
}
