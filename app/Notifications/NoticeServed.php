<?php

namespace App\Notifications;

use App\Mail\NoticeServed as NoticeServedMailable;
use App\Models\Notice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Notification;

class NoticeServed extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The notice instance.
     */
    public Notice $notice;

    /**
     * Create a new notification instance.
     */
    public function __construct(Notice $notice)
    {
        $this->notice = $notice;
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
    public function toMail(object $notifiable): Mailable
    {
        return (new NoticeServedMailable($this->notice, $notifiable))->to($notifiable->email);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'notice_id' => $this->notice->id,
            'notice_type' => $this->notice->noticeType->name,
            'served_date' => now()->toDateTimeString(),
        ];
    }
}
