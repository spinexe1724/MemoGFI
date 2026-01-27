<?php

namespace App\Notifications;

use App\Models\Memo;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MemoApprovalNotification extends Notification
{
    use Queueable;

    protected $memo;
    protected $senderName;

    /**
     * @param Memo $memo
     * @param string $senderName Nama orang yang mengirim/menandatangani terakhir
     */
    public function __construct(Memo $memo, $senderName)
    {
        $this->memo = $memo;
        $this->senderName = $senderName;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $url = route('memos.show', $this->memo->id);

        return (new MailMessage)
            ->subject('Permohonan Persetujuan Memo: ' . $this->memo->subject)
            ->greeting('Halo, ' . $notifiable->name)
            ->line('Ada memo baru yang membutuhkan tanda tangan digital Anda.')
            ->line('**Perihal:** ' . $this->memo->subject)
            ->line('**Nomor Ref:** ' . $this->memo->reference_no)
            ->line('**Dikirim Oleh:** ' . $this->senderName)
            ->action('Lihat & Setujui Memo', $url)
            ->line('Mohon segera ditindaklanjuti untuk kelancaran proses operasional.')
            ->salutation('Salam, Sistem E-Memo Gratama');
    }
}