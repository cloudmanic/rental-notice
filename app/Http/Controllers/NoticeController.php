<?php

namespace App\Http\Controllers;

use App\Models\Notice;
use App\Services\NoticeService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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
     * @return void
     */
    public function __construct(NoticeService $noticeService)
    {
        $this->noticeService = $noticeService;
    }

    /**
     * Generate and serve a PDF for a notice.
     *
     * @return Response
     */
    public function generatePdf(Notice $notice, Request $request)
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

            if (! file_exists($fullPath)) {
                abort(404, 'PDF generation failed');
            }

            // Check if download is requested via URL parameter
            $disposition = 'inline';
            $filename = 'notice-'.uniqid().'.pdf';

            if ($request->has('download') && $request->query('download') === 'true') {
                $disposition = 'attachment';
            }

            // Return the PDF with appropriate Content-Disposition
            return response()->file($fullPath, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => $disposition.'; filename="'.$filename.'"',
            ]);
        } catch (\Exception $e) {
            // Log the error
            Log::error('PDF Generation failed: '.$e->getMessage(), [
                'notice_id' => $notice->id,
                'exception' => $e,
            ]);

            // Return error response
            abort(500, 'Failed to generate PDF: '.$e->getMessage());
        }
    }

    /**
     * Generate and serve a PDF for a shipping confirmation form (PS3817).
     *
     * @return Response
     */
    public function generateShippingForm(Notice $notice, Request $request)
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

            if (! file_exists($fullPath)) {
                abort(404, 'Shipping form generation failed');
            }

            // Check if download is requested via URL parameter
            $disposition = 'inline';
            $filename = 'shipping-form-'.uniqid().'.pdf';

            if ($request->has('download') && $request->query('download') === 'true') {
                $disposition = 'attachment';
            }

            // Return the PDF with appropriate Content-Disposition
            return response()->file($fullPath, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => $disposition.'; filename="'.$filename.'"',
            ]);
        } catch (\Exception $e) {
            // Log the error
            Log::error('Shipping Form PDF Generation failed: '.$e->getMessage(), [
                'notice_id' => $notice->id,
                'exception' => $e,
            ]);

            // Return error response
            abort(500, 'Failed to generate shipping form: '.$e->getMessage());
        }
    }

    /**
     * Generate and serve a complete PDF package that includes both the notice and the shipping form.
     * The shipping form will appear after the notice in the PDF.
     *
     * @return Response
     */
    public function generateCompletePackage(Notice $notice, Request $request)
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

            if (! file_exists($fullPath)) {
                abort(404, 'Complete package generation failed');
            }

            // Check if download is requested via URL parameter
            $disposition = 'inline';
            $filename = 'complete-package-'.uniqid().'.pdf';

            if ($request->has('download') && $request->query('download') === 'true') {
                $disposition = 'attachment';
            }

            // Return the PDF with appropriate Content-Disposition
            return response()->file($fullPath, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => $disposition.'; filename="'.$filename.'"',
            ]);
        } catch (\Exception $e) {
            // Log the error
            Log::error('Complete PDF Package Generation failed: '.$e->getMessage(), [
                'notice_id' => $notice->id,
                'exception' => $e,
            ]);

            // Return error response
            abort(500, 'Failed to generate complete package: '.$e->getMessage());
        }
    }

    /**
     * Serve the certificate PDF for a notice.
     *
     * @return Response
     */
    public function getCertificatePdf(Notice $notice, Request $request)
    {
        // Authorization check - can only view notices in your own account
        if ($notice->account_id !== Auth::user()->account->id) {
            abort(403, 'Unauthorized action.');
        }

        try {
            // Check if certificate PDF exists
            if (empty($notice->certificate_pdf)) {
                abort(404, 'Certificate PDF not found');
            }

            // Check if download is requested via URL parameter
            $disposition = 'inline';
            $filename = 'certificate-'.$notice->id.'.pdf';

            if ($request->has('download') && $request->query('download') === 'true') {
                $disposition = 'attachment';
            }

            // Get the URL to the S3 file
            $url = Storage::disk('s3')->url($notice->certificate_pdf);

            // Get the contents of the file from S3
            $contents = Storage::disk('s3')->get($notice->certificate_pdf);

            if (empty($contents)) {
                abort(404, 'Certificate PDF file could not be retrieved');
            }

            // Return the PDF with appropriate Content-Disposition
            return response($contents, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => $disposition.'; filename="'.$filename.'"',
            ]);
        } catch (\Exception $e) {
            // Log the error
            Log::error('Certificate PDF retrieval failed: '.$e->getMessage(), [
                'notice_id' => $notice->id,
                'certificate_path' => $notice->certificate_pdf,
                'exception' => $e,
            ]);

            // Return error response
            abort(500, 'Failed to retrieve certificate PDF: '.$e->getMessage());
        }
    }

    /**
     * Generate and serve tenant address sheets PDF for all tenants on a notice.
     *
     * @return Response
     */
    public function generateTenantAddressSheets(Notice $notice, Request $request)
    {
        // Authorization check - only super admins can generate address sheets
        if (! Auth::user()->isSuperAdmin() && ! session('impersonating')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            // Generate the combined address sheets PDF using our NoticeService
            $pdfPath = $this->noticeService->generateCombinedAddressSheets($notice);

            // Get the full path to the generated PDF file
            $fullPath = Storage::disk('local')->path($pdfPath);

            if (! file_exists($fullPath)) {
                abort(404, 'Tenant address sheets generation failed');
            }

            // Check if download is requested via URL parameter
            $disposition = 'inline';
            $filename = 'tenant-address-sheets-'.$notice->id.'.pdf';

            if ($request->has('download') && $request->query('download') === 'true') {
                $disposition = 'attachment';
            }

            // Return the PDF with appropriate Content-Disposition
            return response()->file($fullPath, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => $disposition.'; filename="'.$filename.'"',
            ]);
        } catch (\Exception $e) {
            // Log the error
            Log::error('Tenant Address Sheets Generation failed: '.$e->getMessage(), [
                'notice_id' => $notice->id,
                'exception' => $e,
            ]);

            // Return error response
            abort(500, 'Failed to generate tenant address sheets: '.$e->getMessage());
        }
    }

    /**
     * Generate and serve agent address sheet PDF for a notice.
     *
     * @return Response
     */
    public function generateAgentAddressSheet(Notice $notice, Request $request)
    {
        // Authorization check - only super admins can generate address sheets
        if (! Auth::user()->isSuperAdmin() && ! session('impersonating')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            // Generate the agent address sheet PDF using our NoticeService
            $pdfPath = $this->noticeService->generateAgentAddressSheet($notice);

            // Get the full path to the generated PDF file
            $fullPath = Storage::disk('local')->path($pdfPath);

            if (! file_exists($fullPath)) {
                abort(404, 'Agent address sheet generation failed');
            }

            // Check if download is requested via URL parameter
            $disposition = 'inline';
            $filename = 'agent-address-sheet-'.$notice->id.'.pdf';

            if ($request->has('download') && $request->query('download') === 'true') {
                $disposition = 'attachment';
            }

            // Return the PDF with appropriate Content-Disposition
            return response()->file($fullPath, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => $disposition.'; filename="'.$filename.'"',
            ]);
        } catch (\Exception $e) {
            // Log the error
            Log::error('Agent Address Sheet Generation failed: '.$e->getMessage(), [
                'notice_id' => $notice->id,
                'exception' => $e,
            ]);

            // Return error response
            abort(500, 'Failed to generate agent address sheet: '.$e->getMessage());
        }
    }

    /**
     * Generate and serve agent cover letter PDF for a notice.
     *
     * @return Response
     */
    public function generateAgentCoverLetter(Notice $notice, Request $request)
    {
        // Authorization check - only super admins can generate cover letters
        if (! Auth::user()->isSuperAdmin() && ! session('impersonating')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            // Generate the agent cover letter PDF using our NoticeService
            $pdfPath = $this->noticeService->generateAgentCoverLetter($notice);

            // Get the full path to the generated PDF file
            $fullPath = Storage::disk('local')->path($pdfPath);

            if (! file_exists($fullPath)) {
                abort(404, 'Agent cover letter generation failed');
            }

            // Check if download is requested via URL parameter
            $disposition = 'inline';
            $filename = 'agent-cover-letter-'.$notice->id.'.pdf';

            if ($request->has('download') && $request->query('download') === 'true') {
                $disposition = 'attachment';
            }

            // Return the PDF with appropriate Content-Disposition
            return response()->file($fullPath, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => $disposition.'; filename="'.$filename.'"',
            ]);
        } catch (\Exception $e) {
            // Log the error
            Log::error('Agent Cover Letter Generation failed: '.$e->getMessage(), [
                'notice_id' => $notice->id,
                'exception' => $e,
            ]);

            // Return error response
            abort(500, 'Failed to generate agent cover letter: '.$e->getMessage());
        }
    }

    /**
     * Legacy method - maintained for backward compatibility
     *
     * @deprecated Use generateTenantAddressSheets instead
     */
    public function generateAddressSheets(Notice $notice, Request $request)
    {
        return $this->generateTenantAddressSheets($notice, $request);
    }
}
