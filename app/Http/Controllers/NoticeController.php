<?php

namespace App\Http\Controllers;

use App\Models\Notice;
use App\Services\NoticeService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class NoticeController extends Controller
{
    /**
     * The notice service instance.
     *
     * @var NoticeService
     */
    protected $noticeService;

    /**
     * Create a new controller instance.
     *
     * @param NoticeService $noticeService
     * @return void
     */
    public function __construct(NoticeService $noticeService)
    {
        $this->noticeService = $noticeService;
    }

    /**
     * Generate and serve a PDF for a notice.
     * 
     * @param Notice $notice
     * @return Response
     */
    public function generatePdf(Notice $notice)
    {
        // Authorization check - can only view notices in your own account
        if ($notice->account_id !== auth()->user()->account->id) {
            abort(403, 'Unauthorized action.');
        }

        try {
            // Check if watermark is requested
            $watermarked = true;

            // Generate the PDF using our NoticeService
            $pdfPath = $this->noticeService->generatePdfNotice($notice, $watermarked);

            // Get the full path to the generated PDF file
            $fullPath = Storage::path($pdfPath);

            if (!file_exists($fullPath)) {
                abort(404, 'PDF generation failed');
            }

            // Return the PDF as a download
            return response()->file($fullPath, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="notice-' . uniqid() . '.pdf"'
            ]);
        } catch (\Exception $e) {
            // Log the error
            \Log::error('PDF Generation failed: ' . $e->getMessage(), [
                'notice_id' => $notice->id,
                'exception' => $e
            ]);

            // Return error response
            abort(500, 'Failed to generate PDF: ' . $e->getMessage());
        }
    }
}
