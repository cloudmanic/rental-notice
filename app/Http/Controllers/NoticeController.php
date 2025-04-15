<?php

namespace App\Http\Controllers;

use App\Models\Notice;
use App\Services\NoticeService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

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
        if ($notice->account_id !== Auth::user()->account->id) {
            abort(403, 'Unauthorized action.');
        }

        try {
            // Set watermark to false if notice status is SERVICE_PENDING or SERVED
            $watermarked = true;
            if ($notice->status === Notice::STATUS_SERVICE_PENDING || $notice->status === Notice::STATUS_SERVED) {
                $watermarked = false;
            }

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
            Log::error('PDF Generation failed: ' . $e->getMessage(), [
                'notice_id' => $notice->id,
                'exception' => $e
            ]);

            // Return error response
            abort(500, 'Failed to generate PDF: ' . $e->getMessage());
        }
    }

    /**
     * Generate and serve a PDF for a shipping confirmation form (PS3817).
     * 
     * @param Notice $notice
     * @return Response
     */
    public function generateShippingForm(Notice $notice)
    {
        // Authorization check - can only view notices in your own account
        if ($notice->account_id !== Auth::user()->account->id) {
            abort(403, 'Unauthorized action.');
        }

        try {
            // Check if watermark is requested
            $watermarked = true;

            // Generate the shipping form PDF using our NoticeService
            $pdfPath = $this->noticeService->generatePdfShippingForm($notice, $watermarked);

            // Get the full path to the generated PDF file
            $fullPath = Storage::path($pdfPath);

            if (!file_exists($fullPath)) {
                abort(404, 'Shipping form generation failed');
            }

            // Return the PDF as a download
            return response()->file($fullPath, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="shipping-form-' . uniqid() . '.pdf"'
            ]);
        } catch (\Exception $e) {
            // Log the error
            Log::error('Shipping Form PDF Generation failed: ' . $e->getMessage(), [
                'notice_id' => $notice->id,
                'exception' => $e
            ]);

            // Return error response
            abort(500, 'Failed to generate shipping form: ' . $e->getMessage());
        }
    }

    /**
     * Generate and serve a complete PDF package that includes both the notice and the shipping form.
     * The shipping form will appear after the notice in the PDF.
     * 
     * @param Notice $notice
     * @return Response
     */
    public function generateCompletePackage(Notice $notice)
    {
        // Authorization check - can only view notices in your own account
        if ($notice->account_id !== Auth::user()->account->id) {
            abort(403, 'Unauthorized action.');
        }

        try {
            // Check if watermark is requested
            $watermarked = true;

            // Generate the complete PDF package using our NoticeService
            $pdfPath = $this->noticeService->generateCompletePdfPackage($notice, $watermarked);

            // Get the full path to the generated PDF file
            $fullPath = Storage::disk('local')->path($pdfPath);

            if (!file_exists($fullPath)) {
                abort(404, 'Complete package generation failed');
            }

            // Return the PDF as a download
            return response()->file($fullPath, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="complete-package-' . uniqid() . '.pdf"'
            ]);
        } catch (\Exception $e) {
            // Log the error
            Log::error('Complete PDF Package Generation failed: ' . $e->getMessage(), [
                'notice_id' => $notice->id,
                'exception' => $e
            ]);

            // Return error response
            abort(500, 'Failed to generate complete package: ' . $e->getMessage());
        }
    }
}
