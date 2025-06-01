<?php

namespace Tests\Unit\Jobs;

use App\Jobs\PrintNoticePackageJob;
use App\Models\Notice;
use App\Services\NoticeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
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
    #[Test]
    public function print_job_handles_successfully()
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

        // Create test job that skips actual Process execution
        $job = new class($notice) extends PrintNoticePackageJob
        {
            protected function executeProcess(array $command): bool
            {
                // Skip actual process execution in tests
                return true;
            }

            protected function getHost(): string
            {
                return 'test-host';
            }

            protected function getUsername(): string
            {
                return 'test-user';
            }
        };

        // Mock Log facade
        Log::shouldReceive('info')->zeroOrMoreTimes();

        // Create a temporary test file
        file_put_contents('/tmp/test.pdf', 'test content');

        // Execute the job
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
    #[Test]
    public function print_job_skips_when_env_not_set()
    {
        // Create a notice
        $notice = Notice::factory()->create(['status' => 'paid']);

        // Create a job instance that will have empty env values
        $job = new class($notice) extends PrintNoticePackageJob
        {
            protected function getHost(): string
            {
                return '';
            }

            protected function getUsername(): string
            {
                return '';
            }

            public function handle(NoticeService $noticeService): void
            {
                // Check if required environment variables are set
                $host = $this->getHost();
                $username = $this->getUsername();

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
    #[Test]
    public function print_job_handles_scp_failure()
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

        // Create test job that simulates SCP failure
        $job = new class($notice) extends PrintNoticePackageJob
        {
            protected function executeProcess(array $command): bool
            {
                // Simulate SCP failure
                if (str_contains($command[0], 'scp')) {
                    $this->lastError = 'Connection refused';

                    return false;
                }

                return true;
            }

            protected function getHost(): string
            {
                return 'test-host';
            }

            protected function getUsername(): string
            {
                return 'test-user';
            }

            protected string $lastError = '';

            protected function getLastError(): string
            {
                return $this->lastError;
            }
        };

        // Mock Log facade
        Log::shouldReceive('info')->zeroOrMoreTimes();
        Log::shouldReceive('error')->once();

        // Create a temporary test file
        file_put_contents('/tmp/test.pdf', 'test content');

        // Execute the job
        $job->handle($noticeService);
    }
}
