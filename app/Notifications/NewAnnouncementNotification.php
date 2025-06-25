<?php
// app/Notifications/NewAnnouncementNotification.php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Pengumuman; // Import model Pengumuman

class NewAnnouncementNotification extends Notification
{
    use Queueable;

    protected $pengumuman;

    /**
     * Create a new notification instance.
     */
    public function __construct(Pengumuman $pengumuman)
    {
        $this->pengumuman = $pengumuman;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database']; // Hanya simpan ke database
        // Anda juga bisa menambahkan 'mail' jika ingin mengirim email
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'id_pengumuman' => $this->pengumuman->id,
            'judul' => $this->pengumuman->judul,
            'konten_singkat' => \Illuminate\Support\Str::limit(strip_tags($this->pengumuman->konten), 100), // Batasi konten agar tidak terlalu panjang
            // 'url' => route('filament.admin.resources.pengumuman.view', ['record' => $this->pengumuman->id]), // Pastikan rute ini benar
            'tanggal_publikasi' => $this->pengumuman->tanggal_publikasi->format('Y-m-d H:i'),
        ];
    }
}