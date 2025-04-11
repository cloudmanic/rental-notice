<?php

namespace App\Services;

use App\Models\Notice;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class NoticeService
{
    /**
     * Generate a JSON notice file based on a template for the given notice
     * 
     * @param Notice $notice The notice to generate JSON for
     * @return string The path to the generated JSON file in storage
     */
    public function generateJsonNotice(Notice $notice): string
    {
        // Load the template
        $templatePath = base_path('templates/10-day-notice-template.json');
        if (!File::exists($templatePath)) {
            throw new \Exception("Notice template file not found at {$templatePath}");
        }

        $templateJson = File::get($templatePath);
        $template = json_decode($templateJson, true);

        if (!$template) {
            throw new \Exception("Failed to parse notice template JSON");
        }

        // Get the tenants associated with this notice
        $tenants = $notice->tenants()->get();

        if ($tenants->isEmpty()) {
            throw new \Exception("No tenants associated with this notice");
        }

        $agent = $notice->agent;

        if (!$agent) {
            throw new \Exception("No agent associated with this notice");
        }

        // Update template with notice data
        $formData = &$template['forms'][0];

        // Helper function to update textfield by name
        $updateTextField = function (string $name, string $value) use (&$formData) {
            foreach ($formData['textfield'] as &$field) {
                if ($field['name'] === $name) {
                    $field['value'] = $value;
                    break;
                }
            }
        };

        // Helper function to update checkbox by name
        $updateCheckbox = function (string $name, bool $value) use (&$formData) {
            foreach ($formData['checkbox'] as &$field) {
                if ($field['name'] === $name) {
                    $field['value'] = $value;
                    break;
                }
            }
        };

        // Format currency values
        $formatCurrency = function ($amount) {
            return number_format((float)$amount, 2);
        };

        // Calculate the posted date (today unless it's a weekend, then Monday)
        $postedDate = Carbon::now();
        if ($postedDate->isWeekend()) {
            $postedDate = $postedDate->next('Monday');
        }

        // Current date
        $updateTextField('date', $postedDate->format('m/d/Y'));

        // Tenant information
        $primaryTenant = $tenants->first();

        $streetAddress = $primaryTenant->address_1 ?? "";

        if ($primaryTenant->address_2) {
            $streetAddress = $primaryTenant->address_1 . ', ' . $primaryTenant->address_2;
        }

        $updateTextField('propertyName', $primaryTenant->address_1);
        $updateTextField('streetAddress', $streetAddress);
        //$updateTextField('unitNumber', $primaryTenant->address_2 ?? '');
        $updateTextField('city', $primaryTenant->city);
        $updateTextField('state', $primaryTenant->state);
        $updateTextField('zip', $primaryTenant->zip);

        // Add tenants (up to 6)
        $tenantFields = ['tenant1', 'tenant2', 'tenant3', 'tenant4', 'tenant5', 'tenant6'];
        foreach ($tenants as $index => $tenant) {
            if ($index < count($tenantFields)) {
                $updateTextField($tenantFields[$index], $tenant->full_name);
            }
        }

        // Financial information
        $updateTextField('pastDueRent', $formatCurrency($notice->past_due_rent));
        $updateTextField('rentAmountDue', $formatCurrency($notice->past_due_rent));
        $updateTextField('lateCharges', $formatCurrency($notice->late_charges));

        // Other charges
        $otherChargeFields = [
            ['title' => 'other_1_title', 'price' => 'other_1_price', 'desc_field' => 'other1', 'amount_field' => 'otherCharge1'],
            ['title' => 'other_2_title', 'price' => 'other_2_price', 'desc_field' => 'other2', 'amount_field' => 'otherCharge2'],
            ['title' => 'other_3_title', 'price' => 'other_3_price', 'desc_field' => 'other3', 'amount_field' => 'otherCharge3'],
            ['title' => 'other_4_title', 'price' => 'other_4_price', 'desc_field' => 'other4', 'amount_field' => 'otherCharge4'],
            ['title' => 'other_5_title', 'price' => 'other_5_price', 'desc_field' => 'other5', 'amount_field' => 'otherCharge5'],
        ];

        foreach ($otherChargeFields as $charge) {
            $title = $notice->{$charge['title']} ?? '';
            $price = $notice->{$charge['price']} ?? 0;

            if (!empty($title) && $price > 0) {
                $updateTextField($charge['desc_field'], $title);
                $updateTextField($charge['amount_field'], $formatCurrency($price));
            } else {
                $updateTextField($charge['desc_field'], '');
                $updateTextField($charge['amount_field'], '');
            }
        }

        // Total amount due
        $updateTextField('totalOutstandingAmount', $formatCurrency($notice->total_charges));

        // Agent information
        $updateTextField('signature', $agent->name);
        $updateTextField('agentAddress1', $agent->address_1);
        $updateTextField('agentAddress2', $agent->city . ', ' . $agent->state . ' ' . $agent->zip);
        $updateTextField('agentPhone', $agent->phone);
        $updateTextField('agentEmail', $agent->email);

        // Payment method checkbox
        $updateCheckbox('checkBoxFirstClass', true);
        $updateCheckbox('checkBoxOtherFormPayment', $notice->payment_other_means);

        // Calculate the serve by date (15 days from posted date) (start next day, add 4 mailing, 10 service)
        $serveByDate = clone $postedDate;
        $serveByDate->addDays(15);

        // Service date information - just using current date for now, these should be updated when services actually happen
        //$updateTextField('personalServiceDate', $currentDate);
        //$updateTextField('postedServiceDate', $currentDate);
        $updateTextField('firstClassServiceDate', $serveByDate->format('m/d/Y'));
        $updateTextField('addendumServiceDate', $serveByDate->format('m/d/Y'));

        // Add notice ID. This is used internally to match up the notice to a DB record (UII = Unique Internal Identifier)
        $updateTextField('noticeId', "UII:$notice->id");

        // Generate unique filename based on notice ID
        $fileName = 'notice_' . $notice->id . '_' . time() . '.json';
        $storagePath = 'notices/' . $fileName;

        // Make sure the notices directory exists
        Storage::disk('local')->makeDirectory('notices');

        // Store the generated JSON
        Storage::disk('local')->put($storagePath, json_encode($template, JSON_PRETTY_PRINT));

        return $storagePath;
    }

    /**
     * Generate a PDF notice file by filling the PDF template with JSON data
     * 
     * @param Notice $notice The notice to generate PDF for
     * @param bool $watermarked Whether to add a "DRAFT" watermark to the PDF
     * @return string The path to the generated PDF file in storage
     */
    public function generatePdfNotice(Notice $notice, bool $watermarked = false): string
    {
        // First, generate the JSON notice
        $jsonStoragePath = $this->generateJsonNotice($notice);
        $jsonFullPath = Storage::disk('local')->path($jsonStoragePath);

        // Get the PDF template path
        $pdfTemplatePath = base_path('templates/10-day-notice-template.pdf');

        if (!File::exists($pdfTemplatePath)) {
            throw new \Exception("PDF template file not found at {$pdfTemplatePath}");
        }

        // Generate the output PDF filename
        $pdfFileName = 'notice_' . $notice->id . '_' . time() . '.pdf';
        $pdfStoragePath = 'notices/' . $pdfFileName;
        $pdfFullPath = Storage::disk('local')->path($pdfStoragePath);

        // Make sure the output directory exists
        $outputDir = dirname($pdfFullPath);
        if (!File::isDirectory($outputDir)) {
            File::makeDirectory($outputDir, 0755, true);
        }

        // Execute pdfcpu command to fill the form
        $process = new Process([
            'pdfcpu',
            'form',
            'fill',
            $pdfTemplatePath,
            $jsonFullPath,
            $pdfFullPath
        ]);

        $process->run();

        // Check if the process was successful
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        // Check if the PDF was actually created
        if (!File::exists($pdfFullPath)) {
            throw new \Exception("Failed to generate PDF notice");
        }

        // Add watermark if requested
        if ($watermarked) {
            $this->addDraftWatermark($pdfFullPath);
        }

        // Lock the PDF forms so they cannot be edited
        $this->lockPdfForms($pdfFullPath);

        return $pdfStoragePath;
    }

    /**
     * Add a "DRAFT" watermark to the generated PDF
     * 
     * @param string $pdfPath The full path to the PDF file
     * @return void
     */
    private function addDraftWatermark(string $pdfPath): void
    {
        // Create a temporary file path for the watermarked PDF with proper extension
        $tempPath = $pdfPath . '.temp.pdf';

        // Execute pdfcpu command to add watermark
        // The correct syntax according to pdfcpu help documentation
        $process = new Process([
            'pdfcpu',
            'stamp',
            'add',
            '-mode',
            'text',
            '--',
            'Draft Document',
            'scale:1, op:0.6',
            $pdfPath,
            $tempPath
        ]);

        $process->run();

        // Check if the watermark process was successful
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        // Replace the original PDF with the watermarked version
        File::move($tempPath, $pdfPath);
    }

    /**
     * Lock the PDF forms so they cannot be edited
     * 
     * @param string $pdfPath The full path to the PDF file
     * @return void
     */
    private function lockPdfForms(string $pdfPath): void
    {
        // First, lock all form fields to make them read-only
        $process = new Process([
            'pdfcpu',
            'form',
            'lock',
            $pdfPath
        ]);

        $process->run();

        // Check if the form lock process was successful
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        // Now encrypt the PDF with further restrictions
        // Create a temporary file path for the secured PDF
        $tempPath = $pdfPath . '.secured.pdf';

        // Execute pdfcpu command to encrypt the PDF with permissions that prevent form editing
        // We're setting an owner password but restricting permissions for all users
        $process = new Process([
            'pdfcpu',
            'encrypt',
            '-mode=rc4',         // Encryption mode
            '-perm=all',        // No permissions (can't edit, print, copy, etc.)
            '-opw=' . env('PDF_PASSWORD'),       // Owner password
            $pdfPath,            // Source PDF
            $tempPath            // Output PDF
        ]);

        $process->run();

        // Check if the encryption process was successful
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        // Replace the original PDF with the secured version
        File::move($tempPath, $pdfPath);
    }

    /**
     * Mark a notice as served
     * 
     * @param Notice $notice The notice to mark as served
     * @return void
     */
    public function markAsServed($notice)
    {
        $notice->update(['status' => Notice::STATUS_SERVED]);
        // Additional logic...
    }

    /**
     * Generate a JSON shipping confirmation form (PS3817) based on a template for the given notice
     * 
     * @param Notice $notice The notice for which to generate a shipping confirmation
     * @return string The path to the generated JSON file in storage
     */
    public function generateJsonShippingForm(Notice $notice): string
    {
        // Load the template
        $templatePath = base_path('templates/ps3817-form.json');
        if (!File::exists($templatePath)) {
            throw new \Exception("Shipping form template not found at {$templatePath}");
        }

        $templateJson = File::get($templatePath);
        $template = json_decode($templateJson, true);

        if (!$template) {
            throw new \Exception("Failed to parse shipping form template JSON");
        }

        // Update template with notice data
        $formData = &$template['forms'][0];

        // Helper function to update textfield by name
        $updateTextField = function (string $name, ?string $value = '') use (&$formData) {
            // Convert null to empty string
            $value = $value ?? '';

            foreach ($formData['textfield'] as &$field) {
                if ($field['name'] === $name) {
                    $field['value'] = $value;
                    break;
                }
            }
        };

        // Get the tenants and agent
        $tenants = $notice->tenants()->get();

        if ($tenants->isEmpty()) {
            throw new \Exception("No tenants associated with this notice");
        }

        $primaryTenant = $tenants->first();
        $agent = $notice->agent;

        if (!$agent) {
            throw new \Exception("No agent associated with this notice");
        }

        // Set sender information (agent details)
        $updateTextField('fromLine1', $agent->name);
        $updateTextField('fromLine2', $agent->address_1);

        if ($agent->address_2) {
            $updateTextField('fromLine3', $agent->address_2);
            $updateTextField('fromLine4', "{$agent->city}, {$agent->state} {$agent->zip}");
        } else {
            $updateTextField('fromLine3', "{$agent->city}, {$agent->state} {$agent->zip}");
            $updateTextField('fromLine4', $agent->phone);
        }

        // Set recipient information (tenant details)
        // Concatenate all tenant names for the first line
        $tenantNames = $tenants->pluck('full_name')->join(', ');
        $updateTextField('toLine1', $tenantNames);

        // Use the primary tenant's address for the shipping address
        $updateTextField('toLine2', $primaryTenant->address_1);

        if (!empty($primaryTenant->address_2)) {
            $updateTextField('toLine3', $primaryTenant->address_2);
            $updateTextField('toLine4', "{$primaryTenant->city}, {$primaryTenant->state} {$primaryTenant->zip}");
        } else {
            $updateTextField('toLine3', "{$primaryTenant->city}, {$primaryTenant->state} {$primaryTenant->zip}");
            $updateTextField('toLine4', '');
        }

        // Generate unique filename based on notice ID
        $fileName = 'shipping_' . $notice->id . '_' . time() . '.json';
        $storagePath = 'notices/' . $fileName;

        // Make sure the notices directory exists
        Storage::disk('local')->makeDirectory('notices');

        // Store the generated JSON
        Storage::disk('local')->put($storagePath, json_encode($template, JSON_PRETTY_PRINT));

        return $storagePath;
    }

    /**
     * Generate a PDF shipping confirmation form (PS3817) by filling the PDF template with JSON data
     * 
     * @param Notice $notice The notice for which to generate a shipping form
     * @param bool $watermarked Whether to add a "DRAFT" watermark to the PDF
     * @return string The path to the generated PDF file in storage
     */
    public function generatePdfShippingForm(Notice $notice, bool $watermarked = false): string
    {
        // First, generate the JSON for the shipping form
        $jsonStoragePath = $this->generateJsonShippingForm($notice);
        $jsonFullPath = Storage::disk('local')->path($jsonStoragePath);

        // Get the PDF template path
        $pdfTemplatePath = base_path('templates/ps3817-form.pdf');

        if (!File::exists($pdfTemplatePath)) {
            throw new \Exception("Shipping form template not found at {$pdfTemplatePath}");
        }

        // Generate the output PDF filename
        $pdfFileName = 'shipping_' . $notice->id . '_' . time() . '.pdf';
        $pdfStoragePath = 'notices/' . $pdfFileName;
        $pdfFullPath = Storage::disk('local')->path($pdfStoragePath);

        // Make sure the output directory exists
        $outputDir = dirname($pdfFullPath);
        if (!File::isDirectory($outputDir)) {
            File::makeDirectory($outputDir, 0755, true);
        }

        // Execute pdfcpu command to fill the form
        $process = new Process([
            'pdfcpu',
            'form',
            'fill',
            $pdfTemplatePath,
            $jsonFullPath,
            $pdfFullPath
        ]);

        $process->run();

        // Check if the process was successful
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        // Check if the PDF was actually created
        if (!File::exists($pdfFullPath)) {
            throw new \Exception("Failed to generate PDF shipping form");
        }

        // Add watermark if requested
        if ($watermarked) {
            $this->addDraftWatermark($pdfFullPath);
        }

        // Lock the PDF forms so they cannot be edited
        $this->lockPdfForms($pdfFullPath);

        return $pdfStoragePath;
    }

    /**
     * Generate a complete PDF package that includes both the notice and the shipping form
     * 
     * @param Notice $notice The notice to generate the complete package for
     * @param bool $watermarked Whether to add a "DRAFT" watermark to the PDF
     * @return string The path to the generated complete PDF file in storage
     */
    public function generateCompletePdfPackage(Notice $notice, bool $watermarked = false): string
    {
        // Generate both PDFs separately first
        $noticePdfPath = $this->generatePdfNotice($notice, $watermarked);
        $shippingFormPdfPath = $this->generatePdfShippingForm($notice, $watermarked);

        // Get the full paths to both files
        $noticePdfFullPath = Storage::disk('local')->path($noticePdfPath);
        $shippingFormPdfFullPath = Storage::disk('local')->path($shippingFormPdfPath);

        // Generate the output PDF filename for the merged document
        $mergedPdfFileName = 'complete_package_' . $notice->id . '_' . time() . '.pdf';
        $mergedPdfStoragePath = 'notices/' . $mergedPdfFileName;
        $mergedPdfFullPath = Storage::disk('local')->path($mergedPdfStoragePath);

        // Make sure the output directory exists
        $outputDir = dirname($mergedPdfFullPath);
        if (!File::isDirectory($outputDir)) {
            File::makeDirectory($outputDir, 0755, true);
        }

        // Execute pdfcpu command to merge the PDFs
        // Notice PDF first, then shipping form as specified
        $process = new Process([
            'pdfcpu',
            'merge',
            '-mode=create',
            $mergedPdfFullPath,
            $noticePdfFullPath,
            $shippingFormPdfFullPath
        ]);

        $process->run();

        // Check if the merge process was successful
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        // Check if the merged PDF was actually created
        if (!File::exists($mergedPdfFullPath)) {
            throw new \Exception("Failed to generate merged PDF package");
        }

        return $mergedPdfStoragePath;
    }
}
