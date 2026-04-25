<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    /**
     * The password reset token.
     *
     * @var string
     */
    public $token;

    /**
     * Create a new notification instance.
     *
     * @param  string  $token
     * @return void
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('重設您的 皇恩筆記本 帳號密碼')
            ->greeting('您好！')
            ->line('您收到這封郵件是因為我們收到了您帳號的密碼重設請求。')
            ->action('重設密碼', $url)
            ->line('此密碼重設連結將在 ' . config('auth.passwords.'.config('auth.defaults.passwords').'.expire') . ' 分鐘後過期。')
            ->line('如果您沒有請求重設密碼，請忽略此郵件。')
            ->salutation('祝您平安，皇恩筆記本 團隊');
    }
}
