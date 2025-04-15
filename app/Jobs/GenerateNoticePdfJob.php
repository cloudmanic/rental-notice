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
     * Whether to add a watermark to the PDF.
     *
     * @var bool
     */
    protected $watermarked;

    /**
     * Create a new job instance.
     *
     * @param \App\Models\Notice $notice
     * @param bool $watermarked
     */
    public function __construct(Notice $notice, bool $watermarked = false)
    {
        $this->notice = $notice;
        $this->watermarked = $watermarked;
    }

    /**
     * Execute the job.
     */
    public function handle(NoticeService $noticeService): void
    {
        try {
            // Generate the PDF with refresh always set to true
            $pdfPath = $noticeService->generatePdfNotice(
                $this->notice,
                $this->watermarked,
                true // Always refresh
            );

            Log::info('PDF generated successfully', [
                'notice_id' => $this->notice->id,
                'watermarked' => $this->watermarked,
                'path' => $pdfPath
            ]);
        } catch (\Exception $e) {
            Log::error('PDF generation failed', [
                'notice_id' => $this->notice->id,
                'watermarked' => $this->watermarked,
                'error' => $e->getMessage()
            ]);

            throw $e; // Re-throw the exception so Laravel can handle the job failure
        }
    }
}
