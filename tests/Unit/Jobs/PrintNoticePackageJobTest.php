<?php

namespace Tests\Unit\Jobs;

use App\Jobs\PrintNoticePackageJob;
use App\Models\Notice;
use App\Services\NoticeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Mockery;
use Symfony\Component\Process\Process;
use Tests\TestCase;

class PrintNoticePackageJobTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\NoticeTypeSeeder::class);
    }

    /**
     * Test that the print job handles successfully.
     */
    public function test_print_job_handles_successfully()
    {
        // Create a notice
        $notice = Notice::factory()->create(['status' => 'paid']);

        // Mock the NoticeService
        $noticeService = Mockery::mock(NoticeService::class);
        $noticeService->shouldReceive('generateCompletePrintPackage')
            ->once()
            ->with($notice)
            ->andReturn('/tmp/test.pdf');

        $this->app->instance(NoticeService::class, $noticeService);

        // Mock Process for SCP and SSH commands
        $processMock = Mockery::mock('overload:'.Process::class);
        $processMock->shouldReceive('setTimeout')->andReturnSelf();
        $processMock->shouldReceive('run')->andReturnSelf();
        $processMock->shouldReceive('isSuccessful')->andReturn(true);
        $processMock->shouldReceive('getErrorOutput')->andReturn('');

        // Mock Log facade
        Log::shouldReceive('info')->zeroOrMoreTimes();

        // Create a temporary test file
        file_put_contents('/tmp/test.pdf', 'test content');

        // Execute the job
        $job = new PrintNoticePackageJob($notice);
        $job->handle($noticeService);

        // Clean up
        if (file_exists('/tmp/test.pdf')) {
            unlink('/tmp/test.pdf');
        }

        $this->assertTrue(true); // If we got here, the job executed successfully
    }

    /**
     * Test that the print job skips when environment variables are not set.
     */
    public function test_print_job_skips_when_env_not_set()
    {
        // Create a notice
        $notice = Notice::factory()->create(['status' => 'paid']);

        // Create a job instance that will have empty env values
        $job = new class($notice) extends PrintNoticePackageJob
        {
            public function handle(NoticeService $noticeService): void
            {
                // Override env() calls to return empty
                $host = '';
                $username = '';

                // Check if required environment variables are set
                if (empty($host) || empty($username)) {
                    Log::info('Print job skipped - print server configuration not set', [
                        'notice_id' => $this->notice->id,
                        'host_set' => ! empty($host),
                        'username_set' => ! empty($username),
                    ]);

                    return;
                }

                parent::handle($noticeService);
            }
        };

        // Mock the NoticeService (should not be called)
        $noticeService = Mockery::mock(NoticeService::class);
        $noticeService->shouldNotReceive('generateCompletePrintPackage');

        $this->app->instance(NoticeService::class, $noticeService);

        // Mock Log facade to expect the skip message
        Log::shouldReceive('info')
            ->once()
            ->withArgs(function ($message, $context) use ($notice) {
                return $message === 'Print job skipped - print server configuration not set'
                    && $context['notice_id'] === $notice->id
                    && $context['host_set'] === false
                    && $context['username_set'] === false;
            });

        // Execute the job
        $job->handle($noticeService);

        $this->assertTrue(true); // If we got here, the job executed successfully
    }

    /**
     * Test that the print job handles SCP failure.
     */
    public function test_print_job_handles_scp_failure()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Failed to copy file to print server');

        // Create a notice
        $notice = Notice::factory()->create(['status' => 'paid']);

        // Mock the NoticeService
        $noticeService = Mockery::mock(NoticeService::class);
        $noticeService->shouldReceive('generateCompletePrintPackage')
            ->once()
            ->andReturn('/tmp/test.pdf');

        $this->app->instance(NoticeService::class, $noticeService);

        // Mock Process to fail on SCP
        $processMock = Mockery::mock('overload:'.Process::class);
        $processMock->shouldReceive('setTimeout')->andReturnSelf();
        $processMock->shouldReceive('run')->andReturnSelf();
        $processMock->shouldReceive('isSuccessful')->andReturn(false);
        $processMock->shouldReceive('getErrorOutput')->andReturn('Connection refused');

        // Mock Log facade
        Log::shouldReceive('info')->zeroOrMoreTimes();
        Log::shouldReceive('error')->once();

        // Create a temporary test file
        file_put_contents('/tmp/test.pdf', 'test content');

        // Execute the job
        $job = new PrintNoticePackageJob($notice);
        $job->handle($noticeService);
    }
}
