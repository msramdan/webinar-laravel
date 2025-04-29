<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;
use App\Models\Peserta;

class CustomVerifyEmail extends Notification
{
    use Queueable;

    protected $token;
    protected Peserta $peserta;

    /**
     * Create a new notification instance.
     *
     * @param string $token
     * @param Peserta $peserta
     */
    public function __construct(string $token, Peserta $peserta)
    {
        $this->token = $token;
        $this->peserta = $peserta;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $verificationUrl = URL::temporarySignedRoute(
            'panel-peserta.verify.email',
            now()->addMinutes(60),
            ['email' => $notifiable->getEmailForVerification(), 'token' => $this->token]
        );

        return (new MailMessage)
            ->subject('Verifikasi Alamat Email Anda')
            ->greeting('Halo ' . $this->peserta->nama . ',')
            ->line('Terima kasih telah mendaftar. Silakan klik tombol di bawah ini untuk memverifikasi alamat email Anda.')
            ->action('Verifikasi Email', $verificationUrl)
            ->line('Jika Anda tidak membuat akun ini, abaikan email ini.')
            ->salutation('Hormat kami, Tim Aplikasi Seminar');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
