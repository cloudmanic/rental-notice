<?php

namespace App\Jobs;

use App\Models\Notice;
use App\Services\NoticeService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateNoticePdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The notice instance.
     *
     * @var \App\Models\Notice
     */
    protected $notice;

    /**
     * Create a new job instance.
     *
     * @param \App\Models\Notice $notice
     * @param bool $watermarked
     */
    public function __construct(Notice $notice)
    {
        $this->notice = $notice;
    }

    /**
     * Execute the job.
     */
    public function handle(NoticeService $noticeService): void
    {
        // Set watermark to false if notice status is SERVICE_PENDING or SERVED
        $watermarked = true;
        if ($this->notice->status === Notice::STATUS_SERVICE_PENDING || $this->notice->status === Notice::STATUS_SERVED) {
            $watermarked = false;
        }

        try {
            // Generate the PDF with refresh always set to true
            $pdfPath = $noticeService->generatePdfNotice($this->notice, $watermarked, true);

            Log::info('PDF generated successfully', [
                'notice_id' => $this->notice->id,
                'watermarked' => $watermarked,
                'path' => $pdfPath
            ]);
        } catch (\Exception $e) {
            Log::error('PDF generation failed', [
                'notice_id' => $this->notice->id,
                'watermarked' => $watermarked,
                'error' => $e->getMessage()
            ]);

            throw $e; // Re-throw the exception so Laravel can handle the job failure
        }
    }
}
