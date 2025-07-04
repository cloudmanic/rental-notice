<?php

namespace App\Mail;

use App\Models\Notice;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NoticeReminder extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Notice $notice,
        public User $user
    ) {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $tenantNames = $this->notice->tenants->pluck('full_name')->join(', ');

        return new Envelope(
            subject: "Payment Reminder - Notice for {$tenantNames}",
            bcc: config('constants.oregonpastduerent_com.bcc_email'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.notice.reminder',
            with: [
                'notice' => $this->notice,
                'user' => $this->user,
                'tenants' => $this->notice->tenants,
                'propertyAddress' => $this->notice->tenants->first()->address_1 ?? 'N/A',
                'amount' => $this->notice->price,
                'noticeType' => $this->notice->noticeType->name,
                'daysOld' => $this->notice->created_at->diffInDays(now()),
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
