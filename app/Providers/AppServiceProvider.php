<?php

namespace App\Providers;

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
        \Illuminate\Auth\Notifications\ResetPassword::toMailUsing(function (object $notifiable, string $token) {
            $url = url(route('password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));

            return (new \Illuminate\Notifications\Messages\MailMessage)
                ->subject('Yêu cầu Khôi phục Mật khẩu')
                ->greeting('Xin chào!')
                ->line('Bạn nhận được email này vì chúng tôi đã nhận được yêu cầu khôi phục mật khẩu cho tài khoản của bạn.')
                ->action('Khôi phục Mật khẩu', $url)
                ->line('Liên kết khôi phục mật khẩu này sẽ hết hạn trong 60 phút.')
                ->line('Nếu bạn không yêu cầu khôi phục mật khẩu, vui lòng bỏ qua email này và không cần làm gì thêm.')
                ->salutation('Trân trọng, ' . config('app.name'));
        });
    }
}
