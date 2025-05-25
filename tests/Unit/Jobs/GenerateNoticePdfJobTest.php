<?php

namespace Tests\Unit\Jobs;

use App\Jobs\GenerateNoticePdfJob;
use App\Jobs\PrintNoticePackageJob;
use App\Models\Notice;
use App\Services\NoticeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Tests\TestCase;

class GenerateNoticePdfJobTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\NoticeTypeSeeder::class);
    }

    /**
     * Test that print job is dispatched for service pending notices.
     */
    public function test_print_job_dispatched_for_service_pending_notice()
    {
        Queue::fake();
        Storage::fake('s3');
        Storage::fake('local');

        // Create a notice with SERVICE_PENDING status
        $notice = Notice::factory()->create(['status' => Notice::STATUS_SERVICE_PENDING]);

        // Mock the NoticeService
        $noticeService = Mockery::mock(NoticeService::class);
        $noticeService->shouldReceive('generatePdfNotice')
            ->once()
            ->andReturn('notices/test.pdf');
        $noticeService->shouldReceive('generateCertificateOfMailing')
            ->once()
            ->andReturn('notices/certificate.pdf');

        $this->app->instance(NoticeService::class, $noticeService);

        // Create fake files
        Storage::disk('local')->put('notices/test.pdf', 'test pdf content');
        Storage::disk('local')->put('notices/certificate.pdf', 'certificate content');

        // Mock Log
        Log::shouldReceive('info')->zeroOrMoreTimes();

        // Execute the job
        $job = new GenerateNoticePdfJob($notice);
        $job->handle($noticeService);

        // Assert that PrintNoticePackageJob was dispatched
        Queue::assertPushed(PrintNoticePackageJob::class, function ($job) use ($notice) {
            return $job->notice->id === $notice->id;
        });
    }

    /**
     * Test that print job is not dispatched for non-service pending notices.
     */
    public function test_print_job_not_dispatched_for_paid_notice()
    {
        Queue::fake();
        Storage::fake('s3');
        Storage::fake('local');

        // Create a notice with PENDING_PAYMENT status (not service pending)
        $notice = Notice::factory()->create(['status' => Notice::STATUS_PENDING_PAYMENT]);

        // Mock the NoticeService
        $noticeService = Mockery::mock(NoticeService::class);
        $noticeService->shouldReceive('generatePdfNotice')
            ->once()
            ->andReturn('notices/test.pdf');
        $noticeService->shouldReceive('generateCertificateOfMailing')
            ->once()
            ->andReturn('notices/certificate.pdf');

        $this->app->instance(NoticeService::class, $noticeService);

        // Create fake files
        Storage::disk('local')->put('notices/test.pdf', 'test pdf content');
        Storage::disk('local')->put('notices/certificate.pdf', 'certificate content');

        // Mock Log
        Log::shouldReceive('info')->zeroOrMoreTimes();

        // Execute the job
        $job = new GenerateNoticePdfJob($notice);
        $job->handle($noticeService);

        // Assert that PrintNoticePackageJob was NOT dispatched
        Queue::assertNotPushed(PrintNoticePackageJob::class);
    }
}
