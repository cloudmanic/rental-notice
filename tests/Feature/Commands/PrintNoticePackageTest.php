<?php

namespace Tests\Feature\Commands;

use App\Jobs\PrintNoticePackageJob;
use App\Models\Notice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class PrintNoticePackageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\NoticeTypeSeeder::class);
    }
    /**
     * Test print command with valid notice.
     */
    public function test_print_command_with_valid_notice()
    {
        Queue::fake();

        $notice = Notice::factory()->create(['status' => 'paid']);

        $this->artisan('notice:print', ['notice' => $notice->id])
            ->expectsOutput("Dispatching print job for notice {$notice->id}...")
            ->expectsOutput('Print job dispatched successfully. Check the queue worker logs for progress.')
            ->assertExitCode(0);

        Queue::assertPushed(PrintNoticePackageJob::class, function ($job) use ($notice) {
            return $job->notice->id === $notice->id;
        });
    }

    /**
     * Test print command with non-existent notice.
     */
    public function test_print_command_with_nonexistent_notice()
    {
        Queue::fake();

        $this->artisan('notice:print', ['notice' => 99999])
            ->expectsOutput('Notice with ID 99999 not found.')
            ->assertExitCode(1);

        Queue::assertNothingPushed();
    }

    /**
     * Test print command with draft notice and confirmation.
     */
    public function test_print_command_with_draft_notice_confirmed()
    {
        Queue::fake();

        $notice = Notice::factory()->create(['status' => 'draft']);

        $this->artisan('notice:print', ['notice' => $notice->id])
            ->expectsOutput("Notice {$notice->id} has status 'draft'. Only notices with status 'paid', 'service_pending', or 'served' can be printed.")
            ->expectsQuestion('Do you want to continue anyway?', 'yes')
            ->expectsOutput("Dispatching print job for notice {$notice->id}...")
            ->expectsOutput('Print job dispatched successfully. Check the queue worker logs for progress.')
            ->assertExitCode(0);

        Queue::assertPushed(PrintNoticePackageJob::class);
    }

    /**
     * Test print command with draft notice and rejection.
     */
    public function test_print_command_with_draft_notice_rejected()
    {
        Queue::fake();

        $notice = Notice::factory()->create(['status' => 'draft']);

        $this->artisan('notice:print', ['notice' => $notice->id])
            ->expectsOutput("Notice {$notice->id} has status 'draft'. Only notices with status 'paid', 'service_pending', or 'served' can be printed.")
            ->expectsQuestion('Do you want to continue anyway?', false)
            ->assertExitCode(0);

        Queue::assertNothingPushed();
    }
}
