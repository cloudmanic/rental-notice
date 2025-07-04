<?php

namespace Tests\Feature\Commands;

use App\Mail\NoticeReminder;
use App\Models\Notice;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SendNoticeRemindersTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\NoticeTypeSeeder::class);
    }

    /**
     * Test sending reminders to eligible notices.
     */
    public function test_sends_reminders_to_eligible_notices()
    {
        Mail::fake();

        // Create a user and a notice that's 4 days old and pending payment
        $user = User::factory()->create();
        $notice = Notice::factory()->create([
            'status' => Notice::STATUS_PENDING_PAYMENT,
            'user_id' => $user->id,
            'created_at' => Carbon::now()->subDays(4),
            'reminder_sent_at' => null,
        ]);

        $this->artisan('notice:send-reminders', ['--days' => 3])
            ->expectsOutput("Sent reminder for notice #{$notice->id} to {$user->email}")
            ->expectsOutput('Sent 1 reminder emails out of 1 eligible notices.')
            ->assertExitCode(0);

        // Assert mail was sent
        Mail::assertSent(NoticeReminder::class, function ($mail) use ($notice, $user) {
            return $mail->notice->id === $notice->id &&
                   $mail->user->id === $user->id;
        });

        // Assert reminder_sent_at was updated
        $notice->refresh();
        $this->assertNotNull($notice->reminder_sent_at);
    }

    /**
     * Test does not send reminders to notices not old enough.
     */
    public function test_does_not_send_reminders_to_recent_notices()
    {
        Mail::fake();

        // Create a notice that's only 2 days old (less than default 3 days)
        $user = User::factory()->create();
        Notice::factory()->create([
            'status' => Notice::STATUS_PENDING_PAYMENT,
            'user_id' => $user->id,
            'created_at' => Carbon::now()->subDays(2),
            'reminder_sent_at' => null,
        ]);

        $this->artisan('notice:send-reminders', ['--days' => 3])
            ->expectsOutput('No notices found that need reminder emails.')
            ->assertExitCode(0);

        Mail::assertNothingSent();
    }

    /**
     * Test does not send reminders to notices that already had reminders sent.
     */
    public function test_does_not_send_reminders_to_notices_already_reminded()
    {
        Mail::fake();

        // Create a notice that's old enough but already has reminder sent
        $user = User::factory()->create();
        Notice::factory()->create([
            'status' => Notice::STATUS_PENDING_PAYMENT,
            'user_id' => $user->id,
            'created_at' => Carbon::now()->subDays(4),
            'reminder_sent_at' => Carbon::now()->subDay(),
        ]);

        $this->artisan('notice:send-reminders', ['--days' => 3])
            ->expectsOutput('No notices found that need reminder emails.')
            ->assertExitCode(0);

        Mail::assertNothingSent();
    }

    /**
     * Test does not send reminders to notices with different status.
     */
    public function test_does_not_send_reminders_to_non_pending_payment_notices()
    {
        Mail::fake();

        // Create notices with different statuses
        $user = User::factory()->create();

        Notice::factory()->create([
            'status' => Notice::STATUS_SERVICE_PENDING,
            'user_id' => $user->id,
            'created_at' => Carbon::now()->subDays(4),
            'reminder_sent_at' => null,
        ]);

        Notice::factory()->create([
            'status' => Notice::STATUS_SERVED,
            'user_id' => $user->id,
            'created_at' => Carbon::now()->subDays(4),
            'reminder_sent_at' => null,
        ]);

        $this->artisan('notice:send-reminders', ['--days' => 3])
            ->expectsOutput('No notices found that need reminder emails.')
            ->assertExitCode(0);

        Mail::assertNothingSent();
    }

    /**
     * Test custom days parameter.
     */
    public function test_respects_custom_days_parameter()
    {
        Mail::fake();

        $user = User::factory()->create();
        $notice = Notice::factory()->create([
            'status' => Notice::STATUS_PENDING_PAYMENT,
            'user_id' => $user->id,
            'created_at' => Carbon::now()->subDays(1),
            'reminder_sent_at' => null,
        ]);

        // Should send with --days=1
        $this->artisan('notice:send-reminders', ['--days' => 1])
            ->expectsOutput("Sent reminder for notice #{$notice->id} to {$user->email}")
            ->expectsOutput('Sent 1 reminder emails out of 1 eligible notices.')
            ->assertExitCode(0);

        Mail::assertSent(NoticeReminder::class);
    }
}
