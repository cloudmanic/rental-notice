<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SubscribeUserToSendyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The user instance to subscribe.
     */
    public User $user;

    /**
     * The source of the subscription.
     */
    protected string $source;

    /**
     * The user's IP address.
     */
    protected ?string $ipAddress;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user, string $source = 'registration', ?string $ipAddress = null)
    {
        $this->user = $user;
        $this->source = $source;
        $this->ipAddress = $ipAddress;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Skip if Sendy is not configured
        if (! config('services.sendy.url') || ! config('services.sendy.api_key') || ! config('services.sendy.list_id')) {
            Log::warning('Sendy not configured, skipping subscription for user: '.$this->user->email);

            return;
        }

        try {
            // Subscribe to Sendy
            $response = Http::asForm()->post(config('services.sendy.url').'/subscribe', [
                'email' => $this->user->email,
                'list' => config('services.sendy.list_id'),
                'boolean' => 'true',
                'ipaddress' => $this->ipAddress ?? '127.0.0.1',
                'referrer' => config('app.url'),
                'api_key' => config('services.sendy.api_key'),
                'Source' => $this->source,
                'FirstName' => $this->user->first_name,
                'LastName' => $this->user->last_name,
                'name' => "{$this->user->first_name} {$this->user->last_name}",
            ]);

            $result = $response->body();

            Log::info("Sendy subscription result for {$this->user->email}: {$result}");

            // Send Slack notification if configured
            if (config('services.slack.webhook_url')) {
                $this->sendSlackNotification();
            }
        } catch (\Exception $e) {
            Log::error('Failed to subscribe user to Sendy', [
                'email' => $this->user->email,
                'error' => $e->getMessage(),
            ]);

            // Re-throw to allow retry
            throw $e;
        }
    }

    /**
     * Send a Slack notification about the new subscriber.
     */
    protected function sendSlackNotification(): void
    {
        try {
            Http::post(config('services.slack.webhook_url'), [
                'channel' => '#'.config('services.slack.notifications.channel', 'general'),
                'text' => 'New Newsletter Subscriber from '.config('app.name').": {$this->user->email} (Source: {$this->source})",
            ]);
        } catch (\Exception $e) {
            // Don't fail the job if Slack notification fails
            Log::warning('Failed to send Slack notification for new subscriber', [
                'email' => $this->user->email,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public int $backoff = 60;
}
