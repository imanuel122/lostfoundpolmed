<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Pagination\Paginator;

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
        // Waktu relatif berbahasa Indonesia ("1 jam yang lalu")
        Carbon::setLocale('id');

        // Jumlah dan 5 notifikasi terbaru untuk lonceng di navbar
        View::composer('layouts.app', function ($view) {
            if (auth()->check()) {
                $u = auth()->user();
                $view->with('notifCount', $u->unreadNotifications()->count())
                    ->with('notifLatest', $u->notifications()->latest()->limit(5)->get());
            }
        });

        ResetPassword::toMailUsing(function ($notifiable, string $token) {
            $url = route('password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ]);

            return (new MailMessage)
                ->subject('Atur ulang password LostFound Polmed')
                ->greeting('Halo, ' . $notifiable->name . '!')
                ->line('Kami menerima permintaan untuk mengatur ulang password akunmu.')
                ->action('Atur ulang password', $url)
                ->line('Tautan ini berlaku selama 60 menit.')
                ->line('Kalau bukan kamu yang meminta, abaikan saja email ini. Passwordmu tidak berubah.')
                ->salutation('Salam, LostFound Polmed');
        });

        Paginator::defaultView('pagination.custom');
    }
}
