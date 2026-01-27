<?php

namespace App\Notifications;

use App\Models\Memo;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MemoRejectedNotification extends Notification
{
    use Queueable;

    protected $memo;
    protected $rejectorName;
    protected $reason;

    /**
     * @param Memo $memo Objek memo yang ditolak
     * @param string $rejectorName Nama orang yang menolak
     * @param string $reason Alasan penolakan
     */
    public function __construct(Memo $memo, $rejectorName, $reason)
    {
        $this->memo = $memo;
        $this->rejectorName = $rejectorName;
        $this->reason = $reason;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $url = route('memos.show', $this->memo->id);

        return (new MailMessage)
            ->subject('Pemberitahuan: Memo Ditolak - ' . $this->memo->subject)
            ->greeting('Halo, ' . $notifiable->name)
            ->line('Kami menginformasikan bahwa memo Anda telah **DITOLAK** dan memerlukan revisi.')
            ->line('**Perihal:** ' . $this->memo->subject)
            ->line('**Ditolak Oleh:** ' . $this->rejectorName)
            ->line('**Alasan Penolakan:** ' . ($this->reason ?? 'Tidak ada alasan spesifik yang diberikan.'))
            ->action('Lihat & Revisi Memo', $url)
            ->line('Silakan lakukan perbaikan pada isi memo dan ajukan kembali untuk memulai ulang proses persetujuan.')
            ->salutation('Salam, Sistem E-Memo Gratama');
    }
}