<?php

namespace App\Providers;

use App\Models\Pengaturan;
use App\Services\WhatsAppLinkBuilder;
use Carbon\Carbon;
use Illuminate\Foundation\Http\Events\RequestHandled;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        require_once app_path('Support/helpers.php');
    }

    public function boot(): void
    {
        Event::listen(RequestHandled::class, function (RequestHandled $event): void {
            $event->response->headers->set('X-Robots-Tag', 'noindex, nofollow');
        });

        Carbon::setLocale('id');
        View::composer(['layouts.*', 'owner.pengaturan', 'pdf.laporan', 'components.card-mobil', 'publik.*'], function ($view) {
            $view->with('pengaturan', Pengaturan::nilai());
            $view->with('kontakWa', app(WhatsAppLinkBuilder::class)->kontak());
        });
    }
}
