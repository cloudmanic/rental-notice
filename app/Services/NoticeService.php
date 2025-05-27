<?php

namespace App\Services;

use App\Models\Notice;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class NoticeService
{
    protected DateService $dateService;

    public function __construct(DateService $dateService)
    {
        $this->dateService = $dateService;
    }

    /**
     * Generate a JSON notice file based on a template for the given notice
     *
     * @param  Notice  $notice  The notice to generate JSON for
     * @return string The path to the generated JSON file in storage
     */
    public function generateJsonNotice(Notice $notice): string
    {
        // Load the template
        $templatePath = base_path('templates/'.$notice->noticeType->template.'.json');
        if (! File::exists($templatePath)) {
            throw new \Exception("Notice template file not found at {$templatePath}");
        }

        $templateJson = File::get($templatePath);
        $template = json_decode($templateJson, true);

        if (! $template) {
            throw new \Exception('Failed to parse notice template JSON');
        }

        // Get the tenants associated with this notice
        $tenants = $notice->tenants()->get();

        if ($tenants->isEmpty()) {
            throw new \Exception('No tenants associated with this notice');
        }

        $agent = $notice->agent;

        if (! $agent) {
            throw new \Exception('No agent associated with this notice');
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
            return number_format((float) $amount, 2);
        };

        // Calculate the posted date using DateService
        $postedDate = $this->dateService->getNextMailingDate();

        // Current date
        $updateTextField('date', $postedDate->format('m/d/Y'));

        // Tenant information
        $primaryTenant = $tenants->first();

        $streetAddress = $primaryTenant->address_1 ?? '';

        if ($primaryTenant->address_2) {
            $streetAddress = $primaryTenant->address_1.', '.$primaryTenant->address_2;
        }

        $updateTextField('propertyName', $primaryTenant->address_1);
        $updateTextField('streetAddress', $streetAddress);
        // $updateTextField('unitNumber', $primaryTenant->address_2 ?? '');
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

            if (! empty($title) && $price > 0) {
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
        $updateTextField('agentAddress2', $agent->city.', '.$agent->state.' '.$agent->zip);
        $updateTextField('agentPhone', $agent->phone);
        $updateTextField('agentEmail', $agent->email);

        // Payment method checkbox
        $updateCheckbox('checkBoxFirstClass', true);
        $updateCheckbox('checkBoxOtherFormPayment', $notice->payment_other_means);

        // Extract the notice period from the notice type name (10-Day or 13-Day)
        // This matches patterns like "10-Day Notice..." or "13-Day Notice..."
        $noticeDays = 10; // Default to 10 days
        if (preg_match('/(\d+)-Day/i', $notice->noticeType->name, $matches)) {
            $noticeDays = (int) $matches[1];
        }

        // Calculate the serve by date using DateService
        $serveByDate = $this->dateService->calculateServiceDate($postedDate, $noticeDays);

        // Service date information - just using current date for now, these should be updated when services actually happen
        // $updateTextField('personalServiceDate', $currentDate);
        // $updateTextField('postedServiceDate', $currentDate);
        $updateTextField('firstClassServiceDate', $serveByDate->format('m/d/Y'));
        $updateTextField('addendumServiceDate', $serveByDate->format('m/d/Y'));

        // Add notice ID. This is used internally to match up the notice to a DB record (UII = Unique Internal Identifier)
        $updateTextField('noticeId', "UII:$notice->id");

        // Generate unique filename based on notice ID
        $fileName = 'notice_'.$notice->id.'_'.time().'.json';
        $storagePath = 'notices/'.$fileName;

        // Make sure the notices directory exists
        Storage::disk('local')->makeDirectory('notices');

        // Store the generated JSON
        Storage::disk('local')->put($storagePath, json_encode($template, JSON_PRETTY_PRINT));

        return $storagePath;
    }

    /**
     * Generate a PDF notice file by filling the PDF template with JSON data
     *
     * @param  Notice  $notice  The notice to generate PDF for
     * @param  bool  $watermarked  Whether to add a "DRAFT" watermark to the PDF
     * @param  bool  $refresh  Whether to force regenerate the PDF even if it exists
     * @return string The path to the generated PDF file in storage
     */
    public function generatePdfNotice(Notice $notice, bool $watermarked = false, bool $refresh = false): string
    {
        // Check if PDF already exists in the database
        $pdfColumn = $watermarked ? 'draft_pdf' : 'final_pdf';

        if (! $refresh && ! empty($notice->$pdfColumn)) {
            // PDF exists in database, check if we have it locally
            $s3Path = $notice->$pdfColumn;
            $localFileName = basename($s3Path);
            $localStoragePath = 'notices/'.$localFileName;
            $localFullPath = Storage::disk('local')->path($localStoragePath);

            // Check if PDF exists locally
            if (! Storage::disk('local')->exists($localStoragePath)) {
                // PDF not found locally, download from S3
                $s3Contents = Storage::disk('s3')->get($s3Path);

                // Ensure local directory exists
                $localDir = dirname($localFullPath);
                if (! File::isDirectory($localDir)) {
                    File::makeDirectory($localDir, 0755, true);
                }

                // Store the file locally
                Storage::disk('local')->put($localStoragePath, $s3Contents);
            }

            // Return the full local path to the PDF file
            return $localStoragePath;
        }

        // PDF doesn't exist or we're refreshing, generate it
        // First, generate the JSON notice
        $jsonStoragePath = $this->generateJsonNotice($notice);
        $jsonFullPath = Storage::disk('local')->path($jsonStoragePath);

        // Get the PDF template path
        $pdfTemplatePath = base_path('templates/'.$notice->noticeType->template.'.pdf');

        if (! File::exists($pdfTemplatePath)) {
            throw new \Exception("PDF template file not found at {$pdfTemplatePath}");
        }

        // Generate the output PDF filename
        $pdfFileName = 'notice_'.$notice->id.'_'.time().'.pdf';
        $localStoragePath = 'notices/'.($watermarked ? 'drafts/' : 'finals/').$pdfFileName;
        $localFullPath = Storage::disk('local')->path($localStoragePath);

        // Make sure the output directory exists
        $outputDir = dirname($localFullPath);
        if (! File::isDirectory($outputDir)) {
            File::makeDirectory($outputDir, 0755, true);
        }

        // Execute pdfcpu command to fill the form
        $process = new Process([
            'pdfcpu',
            'form',
            'fill',
            $pdfTemplatePath,
            $jsonFullPath,
            $localFullPath,
        ]);

        $process->run();

        // Check if the process was successful
        if (! $process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        // Check if the PDF was actually created
        if (! File::exists($localFullPath)) {
            throw new \Exception('Failed to generate PDF notice');
        }

        // Add watermark if requested
        if ($watermarked) {
            $this->addDraftWatermark($localFullPath);
        }

        // Lock the PDF forms so they cannot be edited
        $this->lockPdfForms($localFullPath);

        // Upload to AWS S3
        $s3Path = $notice->account_id.'/notice_'.($watermarked ? 'draft_' : 'final_').$notice->id.'.pdf';
        $result = Storage::disk('s3')->put($s3Path, file_get_contents($localFullPath));

        if (! $result) {
            throw new \Exception('Failed to upload PDF to S3');
        }

        // Update the notice record
        $notice->update([$pdfColumn => $s3Path]);

        // Return the full local path to the generated PDF
        return $localStoragePath;
    }

    /**
     * Add a "DRAFT" watermark to the generated PDF
     *
     * @param  string  $pdfPath  The full path to the PDF file
     */
    private function addDraftWatermark(string $pdfPath): void
    {
        // Create a temporary file path for the watermarked PDF with proper extension
        $tempPath = $pdfPath.'.temp.pdf';

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
            $tempPath,
        ]);

        $process->run();

        // Check if the watermark process was successful
        if (! $process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        // Replace the original PDF with the watermarked version
        File::move($tempPath, $pdfPath);
    }

    /**
     * Lock the PDF forms so they cannot be edited
     *
     * @param  string  $pdfPath  The full path to the PDF file
     */
    private function lockPdfForms(string $pdfPath): void
    {
        // First, lock all form fields to make them read-only
        $process = new Process([
            'pdfcpu',
            'form',
            'lock',
            $pdfPath,
        ]);

        $process->run();

        // Check if the form lock process was successful
        if (! $process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        // Now encrypt the PDF with further restrictions
        // Create a temporary file path for the secured PDF
        $tempPath = $pdfPath.'.secured.pdf';

        // Execute pdfcpu command to encrypt the PDF with permissions that prevent form editing
        // We're setting an owner password but restricting permissions for all users
        $process = new Process([
            'pdfcpu',
            'encrypt',
            '-mode=rc4',         // Encryption mode
            '-perm=all',        // No permissions (can't edit, print, copy, etc.)
            '-opw='.env('PDF_PASSWORD'),       // Owner password
            $pdfPath,            // Source PDF
            $tempPath,            // Output PDF
        ]);

        $process->run();

        // Check if the encryption process was successful
        if (! $process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        // Replace the original PDF with the secured version
        File::move($tempPath, $pdfPath);
    }

    /**
     * Mark a notice as served
     *
     * @param  Notice  $notice  The notice to mark as served
     * @return void
     */
    public function markAsServed($notice)
    {
        $notice->update(['status' => Notice::STATUS_SERVED]);

        // Log the notice served activity
        $tenantNames = $notice->tenants->pluck('full_name')->join(', ');
        ActivityService::log(
            "{$notice->noticeType->name} notice to {$tenantNames} has been served.",
            null,
            $notice->id,
            null,
            'Notice'
        );
        // Additional logic...
    }

    /**
     * Generate a JSON shipping confirmation form (PS3817) based on a template for the given notice
     *
     * @param  Notice  $notice  The notice for which to generate a shipping confirmation
     * @return string The path to the generated JSON file in storage
     */
    public function generateJsonShippingForm(Notice $notice): string
    {
        // Load the template
        $templatePath = base_path('templates/ps3817-form.json');
        if (! File::exists($templatePath)) {
            throw new \Exception("Shipping form template not found at {$templatePath}");
        }

        $templateJson = File::get($templatePath);
        $template = json_decode($templateJson, true);

        if (! $template) {
            throw new \Exception('Failed to parse shipping form template JSON');
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
            throw new \Exception('No tenants associated with this notice');
        }

        $primaryTenant = $tenants->first();
        $agent = $notice->agent;

        if (! $agent) {
            throw new \Exception('No agent associated with this notice');
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

        if (! empty($primaryTenant->address_2)) {
            $updateTextField('toLine3', $primaryTenant->address_2);
            $updateTextField('toLine4', "{$primaryTenant->city}, {$primaryTenant->state} {$primaryTenant->zip}");
        } else {
            $updateTextField('toLine3', "{$primaryTenant->city}, {$primaryTenant->state} {$primaryTenant->zip}");
            $updateTextField('toLine4', '');
        }

        // Generate unique filename based on notice ID
        $fileName = 'shipping_'.$notice->id.'_'.time().'.json';
        $storagePath = 'notices/'.$fileName;

        // Make sure the notices directory exists
        Storage::disk('local')->makeDirectory('notices');

        // Store the generated JSON
        Storage::disk('local')->put($storagePath, json_encode($template, JSON_PRETTY_PRINT));

        return $storagePath;
    }

    /**
     * Generate a PDF shipping confirmation form (PS3817) by filling the PDF template with JSON data
     *
     * @param  Notice  $notice  The notice for which to generate a shipping form
     * @param  bool  $watermarked  Whether to add a "DRAFT" watermark to the PDF
     * @return string The path to the generated PDF file in storage
     */
    public function generatePdfShippingForm(Notice $notice, bool $watermarked = false): string
    {
        // First, generate the JSON for the shipping form
        $jsonStoragePath = $this->generateJsonShippingForm($notice);
        $jsonFullPath = Storage::disk('local')->path($jsonStoragePath);

        // Get the PDF template path
        $pdfTemplatePath = base_path('templates/ps3817-form.pdf');

        if (! File::exists($pdfTemplatePath)) {
            throw new \Exception("Shipping form template not found at {$pdfTemplatePath}");
        }

        // Generate the output PDF filename
        $pdfFileName = 'shipping_'.$notice->id.'_'.time().'.pdf';
        $pdfStoragePath = 'notices/'.$pdfFileName;
        $pdfFullPath = Storage::disk('local')->path($pdfStoragePath);

        // Make sure the output directory exists
        $outputDir = dirname($pdfFullPath);
        if (! File::isDirectory($outputDir)) {
            File::makeDirectory($outputDir, 0755, true);
        }

        // Execute pdfcpu command to fill the form
        $process = new Process([
            'pdfcpu',
            'form',
            'fill',
            $pdfTemplatePath,
            $jsonFullPath,
            $pdfFullPath,
        ]);

        $process->run();

        // Check if the process was successful
        if (! $process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        // Check if the PDF was actually created
        if (! File::exists($pdfFullPath)) {
            throw new \Exception('Failed to generate PDF shipping form');
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
     * @param  Notice  $notice  The notice to generate the complete package for
     * @param  bool  $watermarked  Whether to add a "DRAFT" watermark to the PDF
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
        $mergedPdfFileName = 'complete_package_'.$notice->id.'_'.time().'.pdf';
        $mergedPdfStoragePath = 'notices/'.$mergedPdfFileName;
        $mergedPdfFullPath = Storage::disk('local')->path($mergedPdfStoragePath);

        // Make sure the output directory exists
        $outputDir = dirname($mergedPdfFullPath);
        if (! File::isDirectory($outputDir)) {
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
            $shippingFormPdfFullPath,
        ]);

        $process->run();

        // Check if the merge process was successful
        if (! $process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        // Check if the merged PDF was actually created
        if (! File::exists($mergedPdfFullPath)) {
            throw new \Exception('Failed to generate merged PDF package');
        }

        return $mergedPdfStoragePath;
    }

    /**
     * Generate a Certificate of Mailing PDF for the given notices
     *
     * @param  array  $notices  Array of Notice models or collection
     * @param  array  $options  Additional options for the certificate
     * @return string The path to the generated PDF file in storage
     */
    public function generateCertificateOfMailing($notices, array $options = []): string
    {
        // Convert to collection if array
        $noticesCollection = collect($notices);

        // Prepare data for the template
        $tenantAddresses = [];
        $noticeType = '';

        foreach ($noticesCollection as $notice) {
            // Get notice type from the first notice
            if (empty($noticeType) && $notice->noticeType) {
                $noticeType = $notice->noticeType->name;
            }

            // Get all tenants for this notice
            $tenants = $notice->tenants()->get();

            foreach ($tenants as $tenant) {
                $address = $tenant->full_name.' - ';
                $address .= $tenant->address_1;
                if ($tenant->address_2) {
                    $address .= ', '.$tenant->address_2;
                }
                $address .= ', '.$tenant->city.', '.$tenant->state.' '.$tenant->zip;

                $tenantAddresses[] = $address;
            }
        }

        // Set default values for options
        $defaultMailingDate = $this->dateService->getNextMailingDate();
        $mailingDate = $options['mailingDate'] ?? $this->dateService->formatMailingDate($defaultMailingDate);

        // Generate PDF from Blade template
        $pdf = Pdf::loadView('pdfs.certificate-of-mailing', [
            'mailingDate' => $mailingDate,
            'noticeType' => $noticeType,
            'tenantAddresses' => $tenantAddresses,
            'companyName' => config('constants.oregonpastduerent_com.company_name'),
            'companyAddress1' => config('constants.oregonpastduerent_com.company_address_1'),
            'companyAddress2' => config('constants.oregonpastduerent_com.company_address_2'),
            'companyCity' => config('constants.oregonpastduerent_com.company_city'),
            'companyState' => config('constants.oregonpastduerent_com.company_state'),
            'companyZip' => config('constants.oregonpastduerent_com.company_zip'),
            'companyPhone' => config('constants.oregonpastduerent_com.company_phone'),
            'companyEmail' => config('constants.oregonpastduerent_com.support_email'),
            'postOfficeName' => config('constants.oregonpastduerent_com.post_office_name'),
            'postOfficeAddress' => config('constants.oregonpastduerent_com.post_office_address'),
        ])
            ->setPaper('letter', 'portrait');

        // Generate unique filename
        $fileName = 'certificate_of_mailing_'.time().'.pdf';
        $storagePath = 'notices/certificates/'.$fileName;

        // Make sure the directory exists
        Storage::disk('local')->makeDirectory('notices/certificates');

        // Save the PDF to storage
        $pdfContent = $pdf->output();
        Storage::disk('local')->put($storagePath, $pdfContent);

        return $storagePath;
    }

    /**
     * Generate Tenant Address Sheets PDF (company to tenant)
     *
     * @param  \App\Models\Tenant  $tenant  The tenant to generate address sheet for
     * @param  \App\Models\Notice  $notice  The notice to get context from
     * @return string The path to the generated PDF file in storage
     */
    public function generateTenantAddressSheets($tenant, $notice): string
    {
        // Get company information from config
        $companyConfig = config('constants.oregonpastduerent_com');

        // Generate PDF from Blade template
        $pdf = Pdf::loadView('pdfs.address-sheets', [
            // Company information (from)
            'fromName' => $companyConfig['company_name'],
            'fromAddress1' => $companyConfig['company_address_1'],
            'fromAddress2' => $companyConfig['company_address_2'],
            'fromCity' => $companyConfig['company_city'],
            'fromState' => $companyConfig['company_state'],
            'fromZip' => $companyConfig['company_zip'],

            // Tenant information (to)
            'toName' => $tenant->full_name,
            'toAddress1' => $tenant->address_1,
            'toAddress2' => $tenant->address_2,
            'toCity' => $tenant->city,
            'toState' => $tenant->state,
            'toZip' => $tenant->zip,
        ]);

        // Generate unique filename
        $fileName = 'tenant_address_sheet_'.$notice->id.'_'.$tenant->id.'_'.time().'.pdf';
        $storagePath = 'notices/address_sheets/'.$fileName;

        // Make sure the directory exists
        Storage::disk('local')->makeDirectory('notices/address_sheets');

        // Save the PDF to storage
        $pdfContent = $pdf->output();
        Storage::disk('local')->put($storagePath, $pdfContent);

        return $storagePath;
    }

    /**
     * Generate Agent Address Sheet PDF (company to agent)
     *
     * @param  \App\Models\Notice  $notice  The notice to get agent information from
     * @return string The path to the generated PDF file in storage
     */
    public function generateAgentAddressSheet($notice): string
    {
        // Get the agent from the notice
        $agent = $notice->agent;

        if (! $agent) {
            throw new \Exception('No agent associated with this notice');
        }

        // Get company information from config
        $companyConfig = config('constants.oregonpastduerent_com');

        // Generate PDF from Blade template
        $pdf = Pdf::loadView('pdfs.address-sheets', [
            // Company information (from)
            'fromName' => $companyConfig['company_name'],
            'fromAddress1' => $companyConfig['company_address_1'],
            'fromAddress2' => $companyConfig['company_address_2'],
            'fromCity' => $companyConfig['company_city'],
            'fromState' => $companyConfig['company_state'],
            'fromZip' => $companyConfig['company_zip'],

            // Agent information (to)
            'toName' => $agent->name,
            'toAddress1' => $agent->address_1,
            'toAddress2' => $agent->address_2,
            'toCity' => $agent->city,
            'toState' => $agent->state,
            'toZip' => $agent->zip,
        ]);

        // Generate unique filename
        $fileName = 'agent_address_sheet_'.$notice->id.'_'.time().'.pdf';
        $storagePath = 'notices/address_sheets/'.$fileName;

        // Make sure the directory exists
        Storage::disk('local')->makeDirectory('notices/address_sheets');

        // Save the PDF to storage
        $pdfContent = $pdf->output();
        Storage::disk('local')->put($storagePath, $pdfContent);

        return $storagePath;
    }

    /**
     * Legacy method - maintained for backward compatibility
     *
     * @deprecated Use generateTenantAddressSheets instead
     */
    public function generateAddressSheets($tenant, $notice): string
    {
        return $this->generateTenantAddressSheets($tenant, $notice);
    }

    /**
     * Generate a combined Address Sheets PDF for all tenants on a notice
     *
     * @param  \App\Models\Notice  $notice  The notice to generate address sheets for
     * @return string The path to the generated PDF file in storage
     */
    public function generateCombinedAddressSheets($notice): string
    {
        // Get all tenants for this notice
        $tenants = $notice->tenants()->get();

        if ($tenants->isEmpty()) {
            throw new \Exception('No tenants associated with this notice');
        }

        // Generate individual PDFs for each tenant
        $individualPdfs = [];
        foreach ($tenants as $tenant) {
            $pdfPath = $this->generateTenantAddressSheets($tenant, $notice);
            $individualPdfs[] = Storage::disk('local')->path($pdfPath);
        }

        // Generate the output filename for the combined PDF
        $combinedFileName = 'combined_address_sheets_'.$notice->id.'_'.time().'.pdf';
        $combinedStoragePath = 'notices/address_sheets/'.$combinedFileName;
        $combinedFullPath = Storage::disk('local')->path($combinedStoragePath);

        // Make sure the output directory exists
        $outputDir = dirname($combinedFullPath);
        if (! File::isDirectory($outputDir)) {
            File::makeDirectory($outputDir, 0755, true);
        }

        // If there's only one tenant, just copy the file
        if (count($individualPdfs) === 1) {
            File::copy($individualPdfs[0], $combinedFullPath);
        } else {
            // Merge all PDFs using pdfcpu
            $process = new Process(array_merge(
                ['pdfcpu', 'merge', '-mode=create', $combinedFullPath],
                $individualPdfs
            ));

            $process->run();

            // Check if the merge process was successful
            if (! $process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }
        }

        // Clean up individual PDFs
        foreach ($individualPdfs as $pdfPath) {
            if (File::exists($pdfPath)) {
                File::delete($pdfPath);
            }
        }

        return $combinedStoragePath;
    }

    /**
     * Generate Agent Cover Letter PDF explaining the enclosed documents
     *
     * @param  \App\Models\Notice  $notice  The notice to generate cover letter for
     * @return string The path to the generated PDF file in storage
     */
    public function generateAgentCoverLetter($notice): string
    {
        // Get the agent from the notice
        $agent = $notice->agent;

        if (! $agent) {
            throw new \Exception('No agent associated with this notice');
        }

        // Get company information from config
        $companyConfig = config('constants.oregonpastduerent_com');

        // Get tenants for the notice
        $tenants = $notice->tenants()->get();

        // Get current date
        $currentDate = Carbon::now()->format('F j, Y');

        // Determine how to address the agent
        $agentSalutation = 'Agent';
        if ($agent->first_name && $agent->last_name) {
            $agentSalutation = $agent->first_name.' '.$agent->last_name;
        } elseif ($agent->first_name) {
            $agentSalutation = $agent->first_name;
        } elseif ($agent->last_name) {
            $agentSalutation = $agent->last_name;
        }

        // Generate PDF from Blade template
        $pdf = Pdf::loadView('pdfs.agent-cover-letter', [
            // Company information
            'companyName' => $companyConfig['company_name'],
            'companyAddress1' => $companyConfig['company_address_1'],
            'companyAddress2' => $companyConfig['company_address_2'],
            'companyCity' => $companyConfig['company_city'],
            'companyState' => $companyConfig['company_state'],
            'companyZip' => $companyConfig['company_zip'],
            'companyPhone' => $companyConfig['company_phone'],
            'companyEmail' => $companyConfig['support_email'],
            'portalUrl' => $companyConfig['portal_url'],

            // Agent information
            'agentName' => $agentSalutation,
            'agentAddress1' => $agent->address_1,
            'agentAddress2' => $agent->address_2,
            'agentCity' => $agent->city,
            'agentState' => $agent->state,
            'agentZip' => $agent->zip,

            // Letter content
            'currentDate' => $currentDate,
            'tenantCount' => $tenants->count(),
            'noticeType' => $notice->noticeType->name,
        ]);

        // Generate unique filename
        $fileName = 'agent_cover_letter_'.$notice->id.'_'.time().'.pdf';
        $storagePath = 'notices/cover_letters/'.$fileName;

        // Make sure the directory exists
        Storage::disk('local')->makeDirectory('notices/cover_letters');

        // Save the PDF to storage
        $pdfContent = $pdf->output();
        Storage::disk('local')->put($storagePath, $pdfContent);

        return $storagePath;
    }

    /**
     * Generate Complete Print Package PDF with all documents in proper order
     *
     * @param  \App\Models\Notice  $notice  The notice to generate print package for
     * @return string The path to the generated PDF file in storage
     */
    public function generateCompletePrintPackage($notice): string
    {
        // Get all tenants for this notice
        $tenants = $notice->tenants()->get();

        if ($tenants->isEmpty()) {
            throw new \Exception('No tenants associated with this notice');
        }

        // Array to store all individual PDF paths
        $individualPdfs = [];

        // First, generate documents for each tenant
        foreach ($tenants as $tenant) {
            // 1. Address sheet for tenant
            $tenantAddressSheetPath = $this->generateTenantAddressSheets($tenant, $notice);
            $individualPdfs[] = Storage::disk('local')->path($tenantAddressSheetPath);

            // 2. Copy of notice (we need to generate this for each tenant)
            $noticePdfPath = $this->generatePdfNotice($notice, false);
            $individualPdfs[] = Storage::disk('local')->path($noticePdfPath);
        }

        // Then add agent-related documents
        // 3. Address sheet for agent
        $agentAddressSheetPath = $this->generateAgentAddressSheet($notice);
        $individualPdfs[] = Storage::disk('local')->path($agentAddressSheetPath);

        // 4. Agent cover letter
        $agentCoverLetterPath = $this->generateAgentCoverLetter($notice);
        $individualPdfs[] = Storage::disk('local')->path($agentCoverLetterPath);

        // 5. Another copy of notice for agent
        $agentNoticePdfPath = $this->generatePdfNotice($notice, false);
        $individualPdfs[] = Storage::disk('local')->path($agentNoticePdfPath);

        // 6. Certificate of mailing (if it exists)
        if ($notice->certificate_pdf) {
            // Get the certificate from S3
            $certificateContents = Storage::disk('s3')->get($notice->certificate_pdf);

            // Save it temporarily to local storage
            $tempCertificatePath = 'notices/temp/certificate_'.$notice->id.'_'.time().'.pdf';
            Storage::disk('local')->put($tempCertificatePath, $certificateContents);
            $individualPdfs[] = Storage::disk('local')->path($tempCertificatePath);
        } else {
            // Generate certificate if it doesn't exist
            $certificatePath = $this->generateCertificateOfMailing([$notice]);
            $individualPdfs[] = Storage::disk('local')->path($certificatePath);
        }

        // Generate the output filename for the complete package
        $packageFileName = 'complete_print_package_'.$notice->id.'_'.time().'.pdf';
        $packageStoragePath = 'notices/print_packages/'.$packageFileName;
        $packageFullPath = Storage::disk('local')->path($packageStoragePath);

        // Make sure the output directory exists
        $outputDir = dirname($packageFullPath);
        if (! File::isDirectory($outputDir)) {
            File::makeDirectory($outputDir, 0755, true);
        }

        // Merge all PDFs using pdfcpu
        $process = new Process(array_merge(
            ['pdfcpu', 'merge', '-mode=create', $packageFullPath],
            $individualPdfs
        ));

        $process->run();

        // Check if the merge process was successful
        if (! $process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        // Flatten the PDF to ensure form fields are rendered
        $flattenedPath = str_replace('.pdf', '_flattened.pdf', $packageFullPath);

        $pdftKPath = env('PDFTK_PATH', '/usr/bin/pdftk');

        if (! File::exists($pdftKPath)) {
            throw new \Exception("pdftk not found at {$pdftKPath}. Please install pdftk or set PDFTK_PATH in your .env file.");
        }

        Log::info('Using pdftk to flatten PDF', ['path' => $packageFullPath]);

        // Use pdftk to flatten
        $flattenProcess = new Process([
            $pdftKPath,
            $packageFullPath,
            'output',
            $flattenedPath,
            'flatten',
        ]);

        $flattenProcess->run();

        // If flattening succeeded, replace the original with the flattened version
        if ($flattenProcess->isSuccessful()) {
            File::move($flattenedPath, $packageFullPath, true);
            Log::info('PDF flattened successfully', ['path' => $packageFullPath]);
        } else {
            // Log warning but continue with unflattened PDF
            Log::warning('Failed to flatten PDF', [
                'error' => $flattenProcess->getErrorOutput(),
                'path' => $packageFullPath,
            ]);
        }

        // Clean up temporary files
        foreach ($individualPdfs as $pdfPath) {
            if (File::exists($pdfPath)) {
                // Only delete files in temp or those we generated for this process
                if (strpos($pdfPath, '/temp/') !== false || strpos($pdfPath, time()) !== false) {
                    File::delete($pdfPath);
                }
            }
        }

        return $packageStoragePath;
    }
}
