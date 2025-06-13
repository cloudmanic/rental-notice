<?php

namespace App\Notifications;

use App\Mail\WelcomeEmail;
use App\Models\Referrer;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
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
     * The referrer instance (optional).
     */
    public ?Referrer $referrer;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $user, string $accountName, ?Referrer $referrer = null)
    {
        $this->user = $user;
        $this->accountName = $accountName;
        $this->referrer = $referrer;
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
        return (new WelcomeEmail($this->user))->to($notifiable->email);
    }

    /**
     * Get the Slack representation of the notification.
     */
    public function toSlack(object $notifiable): SlackMessage
    {
        $fields = [
            [
                "type" => "mrkdwn",
                "text" => "*Name:*\\n{$this->user->first_name} {$this->user->last_name}"
            ],
            [
                "type" => "mrkdwn",
                "text" => "*Email:*\\n{$this->user->email}"
            ],
            [
                "type" => "mrkdwn",
                "text" => "*Account:*\\n{$this->accountName}"
            ],
            [
                "type" => "mrkdwn",
                "text" => "*Registered At:*\\n{$this->user->created_at->format('Y-m-d H:i:s')}"
            ]
        ];

        // Add referrer information if available
        if ($this->referrer) {
            $fields[] = [
                "type" => "mrkdwn",
                "text" => "*Referred By:*\\n{$this->referrer->full_name} ({$this->referrer->email})"
            ];
            $discountAmount = number_format($this->referrer->discount_amount, 2);
            $discountPercentage = round(($this->referrer->discount_amount / 15.00) * 100);
            $fields[] = [
                "type" => "mrkdwn",
                "text" => "*Referral Discount:*\\n\${$discountAmount} off ({$discountPercentage}%)"
            ];
        }

        $blocks = [
            [
                "type" => "divider"
            ],
            [
                "type" => "header",
                "text" => [
                    "type" => "plain_text",
                    "text" => $this->referrer ? "🎉 New Referral Registration" : "🎉 New User Registration",
                    "emoji" => true
                ]
            ],
            [
                "type" => "section",
                "fields" => $fields
            ],
            [
                "type" => "divider"
            ]
        ];

        $template = json_encode(["blocks" => $blocks]);

        return (new SlackMessage)->usingBlockKitTemplate($template);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $data = [
            'user_id' => $this->user->id,
            'user_name' => "{$this->user->first_name} {$this->user->last_name}",
            'user_email' => $this->user->email,
            'account_name' => $this->accountName,
        ];

        if ($this->referrer) {
            $data['referrer_id'] = $this->referrer->id;
            $data['referrer_name'] = $this->referrer->full_name;
            $data['referrer_email'] = $this->referrer->email;
            $data['referral_discount'] = $this->referrer->discount_amount;
        }

        return $data;
    }
}
