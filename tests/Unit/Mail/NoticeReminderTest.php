<?php

namespace Tests\Unit\Mail;

use App\Mail\NoticeReminder;
use App\Models\Notice;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NoticeReminderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\NoticeTypeSeeder::class);
    }

    /**
     * Test notice reminder email can be built.
     */
    public function test_notice_reminder_email_can_be_built()
    {
        $user = User::factory()->create();
        $notice = Notice::factory()->create(['user_id' => $user->id]);
        $tenant = Tenant::factory()->create();
        $notice->tenants()->attach($tenant);

        $mailable = new NoticeReminder($notice, $user);

        $content = $mailable->content();
        $envelope = $mailable->envelope();

        $this->assertEquals('mail.notice.reminder', $content->view);
        $this->assertStringContainsString('Payment Reminder', $envelope->subject);
        $this->assertArrayHasKey('notice', $content->with);
        $this->assertArrayHasKey('user', $content->with);
        $this->assertArrayHasKey('tenants', $content->with);
        $this->assertArrayHasKey('daysOld', $content->with);
    }

    /**
     * Test notice reminder email subject includes tenant names.
     */
    public function test_notice_reminder_email_subject_includes_tenant_names()
    {
        $user = User::factory()->create();
        $notice = Notice::factory()->create(['user_id' => $user->id]);

        $tenant1 = Tenant::factory()->create(['first_name' => 'John', 'last_name' => 'Doe']);
        $tenant2 = Tenant::factory()->create(['first_name' => 'Jane', 'last_name' => 'Smith']);

        $notice->tenants()->attach([$tenant1->id, $tenant2->id]);

        $mailable = new NoticeReminder($notice, $user);
        $envelope = $mailable->envelope();

        $this->assertStringContainsString('John Doe, Jane Smith', $envelope->subject);
    }

    /**
     * Test notice reminder email includes correct days old calculation.
     */
    public function test_notice_reminder_email_includes_correct_days_old()
    {
        $user = User::factory()->create();
        $notice = Notice::factory()->create([
            'user_id' => $user->id,
            'created_at' => now()->subDays(5),
        ]);

        $mailable = new NoticeReminder($notice, $user);
        $content = $mailable->content();

        // Days old should be approximately 5, allow for some variation due to timing
        $this->assertGreaterThanOrEqual(4, $content->with['daysOld']);
        $this->assertLessThanOrEqual(6, $content->with['daysOld']);
    }
}
