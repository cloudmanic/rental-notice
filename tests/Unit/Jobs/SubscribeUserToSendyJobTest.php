<?php

namespace Tests\Unit\Jobs;

use App\Jobs\SubscribeUserToSendyJob;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SubscribeUserToSendyJobTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Configure Sendy for tests
        config([
            'services.sendy.url' => 'https://sendy.example.com',
            'services.sendy.api_key' => 'test-api-key',
            'services.sendy.list_id' => 'test-list-id',
            'services.slack.webhook_url' => 'https://hooks.slack.com/test',
            'services.slack.notifications.channel' => 'test-channel',
        ]);
    }

    #[Test]
    public function it_subscribes_user_to_sendy_successfully()
    {
        Http::fake([
            'sendy.example.com/*' => Http::response('1', 200),
            'hooks.slack.com/*' => Http::response('ok', 200),
        ]);

        $user = User::factory()->create([
            'email' => 'test@example.com',
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        $job = new SubscribeUserToSendyJob($user, 'test-source', '127.0.0.1');
        $job->handle();

        Http::assertSent(function ($request) {
            return $request->url() === 'https://sendy.example.com/subscribe' &&
                   $request['email'] === 'test@example.com' &&
                   $request['list'] === 'test-list-id' &&
                   $request['api_key'] === 'test-api-key' &&
                   $request['FirstName'] === 'John' &&
                   $request['LastName'] === 'Doe' &&
                   $request['Source'] === 'test-source' &&
                   $request['ipaddress'] === '127.0.0.1';
        });

        // Assert Slack notification was sent
        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'hooks.slack.com') &&
                   str_contains($request['text'], 'test@example.com');
        });
    }

    #[Test]
    public function it_logs_warning_when_sendy_is_not_configured()
    {
        Log::shouldReceive('warning')
            ->once()
            ->withArgs(function ($message) {
                return str_contains($message, 'Sendy not configured');
            });

        // Clear Sendy configuration
        config([
            'services.sendy.url' => null,
            'services.sendy.api_key' => null,
            'services.sendy.list_id' => null,
        ]);

        $user = User::factory()->create();
        $job = new SubscribeUserToSendyJob($user);
        $job->handle();

        Http::assertNothingSent();
    }

    #[Test]
    public function it_handles_sendy_api_errors_gracefully()
    {
        Log::shouldReceive('error')
            ->once()
            ->withArgs(function ($message, $context) {
                return $message === 'Failed to subscribe user to Sendy' &&
                       isset($context['email']) &&
                       isset($context['error']);
            });

        Http::fake([
            'sendy.example.com/*' => Http::response('Error', 500),
        ]);

        $user = User::factory()->create();
        $job = new SubscribeUserToSendyJob($user);

        $this->expectException(\Exception::class);
        $job->handle();
    }

    #[Test]
    public function it_does_not_fail_job_when_slack_notification_fails()
    {
        // Ensure Slack webhook is configured for this test
        config(['services.slack.webhook_url' => 'https://hooks.slack.com/test']);

        Log::spy();

        Http::fake([
            'sendy.example.com/*' => Http::response('1', 200),
            'hooks.slack.com/*' => Http::response('error', 500),
        ]);

        $user = User::factory()->create();
        $job = new SubscribeUserToSendyJob($user);

        // Should not throw exception
        $job->handle();

        Http::assertSentCount(2);

        // Assert Sendy subscription was attempted
        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'sendy.example.com');
        });

        // Assert Slack notification was attempted
        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'hooks.slack.com');
        });
    }

    #[Test]
    public function it_can_be_dispatched_to_queue()
    {
        Queue::fake();

        $user = User::factory()->create();

        SubscribeUserToSendyJob::dispatch($user, 'registration', '127.0.0.1');

        Queue::assertPushed(SubscribeUserToSendyJob::class, function ($job) use ($user) {
            return $job->user->id === $user->id;
        });
    }

    #[Test]
    public function it_has_retry_configuration()
    {
        $user = User::factory()->create();
        $job = new SubscribeUserToSendyJob($user);

        $this->assertEquals(3, $job->tries);
        $this->assertEquals(60, $job->backoff);
    }
}
