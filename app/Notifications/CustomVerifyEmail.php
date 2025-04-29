<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;
use App\Models\Peserta; // Pastikan model Peserta di-import

class CustomVerifyEmail extends Notification implements ShouldQueue // Implementasi ShouldQueue agar email dikirim di background
{
    use Queueable;

    protected $token;
    protected Peserta $peserta; // Tambahkan properti untuk menyimpan data peserta

    /**
     * Create a new notification instance.
     *
     * @param string $token
     * @param Peserta $peserta
     */
    public function __construct(string $token, Peserta $peserta) // Terima objek Peserta
    {
        $this->token = $token;
        $this->peserta = $peserta; // Simpan objek Peserta
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
        // Buat URL verifikasi sesuai format yang Anda inginkan
        // Pastikan APP_URL di .env sudah benar
        // Gunakan route() jika Anda mendefinisikan route bernama
        $verificationUrl = URL::temporarySignedRoute(
            'panel-peserta.verify.email', // Nama route (akan dibuat di langkah 7)
            now()->addMinutes(60), // Link berlaku selama 60 menit
            ['email' => $notifiable->getEmailForVerification(), 'token' => $this->token]
        );

        // Custom URL seperti permintaan awal (kurang aman tanpa signed URL)
        // $verificationUrl = config('app.url') . "/verified-peserta?email=" . urlencode($notifiable->getEmailForVerification()) . "&token=" . $this->token;


        return (new MailMessage)
            ->subject('Verifikasi Alamat Email Anda') // Judul Email
            ->greeting('Halo ' . $this->peserta->nama . ',') // Sapaan
            ->line('Terima kasih telah mendaftar. Silakan klik tombol di bawah ini untuk memverifikasi alamat email Anda.') // Baris 1
            ->action('Verifikasi Email', $verificationUrl) // Tombol Aksi
            ->line('Jika Anda tidak membuat akun ini, abaikan email ini.') // Baris 2
            ->salutation('Hormat kami, Tim Aplikasi Seminar'); // Salam Penutup
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
