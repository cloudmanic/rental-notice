<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Slack\SlackMessage;

class UserRegistered extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The user instance.
     */
    public User $user;

    /**
     * The account name.
     */
    public string $accountName;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $user, string $accountName)
    {
        $this->user = $user;
        $this->accountName = $accountName;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['slack'];
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
                        "text": "🎉 New User Registration",
                        "emoji": true
                    }
                },
                {
                    "type": "section",
                    "fields": [
                        {
                            "type": "mrkdwn",
                            "text": "*Name:*\\n{$this->user->first_name} {$this->user->last_name}"
                        },
                        {
                            "type": "mrkdwn",
                            "text": "*Email:*\\n{$this->user->email}"
                        },
                        {
                            "type": "mrkdwn",
                            "text": "*Account:*\\n{$this->accountName}"
                        },
                        {
                            "type": "mrkdwn",
                            "text": "*Registered At:*\\n{$this->user->created_at->format('Y-m-d H:i:s')}"
                        }
                    ]
                },
                {
                    "type": "divider"
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
            'user_id' => $this->user->id,
            'user_name' => "{$this->user->first_name} {$this->user->last_name}",
            'user_email' => $this->user->email,
            'account_name' => $this->accountName,
        ];
    }
}
