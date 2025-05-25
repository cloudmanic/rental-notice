<?php

namespace App\Notifications;

use App\Mail\NoticePaid as InvoicePaidMailable;
use App\Models\Notice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Slack\SlackMessage;

class NoticePaid extends Notification implements ShouldQueue
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
        return ['mail', 'slack'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): Mailable
    {
        return (new InvoicePaidMailable($this->notice, $notifiable))->to($notifiable->email);
    }

    /**
     * Get the Slack representation of the notification.
     */
    public function toSlack(object $notifiable): SlackMessage
    {
        $template = <<<JSON
        {
            "blocks": [
                {
                    "type": "divider"
                },
                {
                    "type": "header",
                    "text": {
                        "type": "plain_text",
                        "text": "New Oregon Late Rent Notice",
                        "emoji": true
                    }
                },
                {
                    "type": "rich_text",
                    "elements": [
                        {
                            "type": "rich_text_section",
                            "elements": [
                                {
                                    "type": "text",
                                    "text": "Notice {$this->notice->id} is ready to be served."
                                }
                            ]
                        }
                    ]
                },
                {
                    "type": "actions",
                    "elements": [
                        {
                            "type": "button",
                            "text": {
                                "type": "plain_text",
                                "text": "View Notice",
                                "emoji": true
                            },
                            "value": "click_me_123",
                            "url": "https://oregonpastduerent.com"
                        },
                        {
                            "type": "button",
                            "text": {
                                "type": "plain_text",
                                "text": "Print Notice",
                                "emoji": true
                            },
                            "value": "click_me_123",
                            "url": "https://oregonpastduerent.com"
                        }
                    ]
                }
            ]
        }
        JSON;

        return (new SlackMessage)->usingBlockKitTemplate($template);
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
