<?php

namespace App\Http\Controllers;

use App\Models\Notice;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class NoticeController extends Controller
{
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

        // This is a placeholder. In a real implementation, you would:
        // 1. Generate the PDF using a library like DOMPDF, TCPDF, or Snappy PDF
        // 2. Return the PDF content

        // For demo purposes, we'll just serve a static PDF from the templates folder
        // You'll need to implement the actual PDF generation based on the notice data
        $pdfPath = storage_path('app/templates/10-day-notice-template.pdf');

        if (!file_exists($pdfPath)) {
            abort(404, 'PDF template not found');
        }

        return response()->file($pdfPath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="notice-' . $notice->id . '.pdf"'
        ]);
    }
}
