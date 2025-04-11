<?php

namespace App\Services;

use App\Models\Notice;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

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
            return '$' . number_format((float)$amount, 2);
        };

        // Current date
        $updateTextField('date', Carbon::now()->format('F j, Y'));

        // Tenant information
        $primaryTenant = $tenants->first();
        $updateTextField('streetAddress', $primaryTenant->address_1);
        $updateTextField('unitNumber', $primaryTenant->address_2 ?? '');
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
            ['title' => 'other_1_title', 'price' => 'other_1_price', 'field' => 'other1'],
            ['title' => 'other_2_title', 'price' => 'other_2_price', 'field' => 'other2'],
            ['title' => 'other_3_title', 'price' => 'other_3_price', 'field' => 'other3'],
            ['title' => 'other_4_title', 'price' => 'other_4_price', 'field' => 'other4'],
            ['title' => 'other_5_title', 'price' => 'other_5_price', 'field' => 'other5'],
        ];

        foreach ($otherChargeFields as $charge) {
            $title = $notice->{$charge['title']} ?? '';
            $price = $notice->{$charge['price']} ?? 0;

            if (!empty($title) && $price > 0) {
                $updateTextField($charge['field'], $title . ': ' . $formatCurrency($price));
            } else {
                $updateTextField($charge['field'], '');
            }
        }

        // Total amount due
        $updateTextField('totalOutstandingAmount', $formatCurrency($notice->total_charges));

        // Agent information
        $updateTextField('agentAddress1', $agent->address_1);
        $updateTextField('agentAddress2', $agent->city . ', ' . $agent->state . ' ' . $agent->zip);
        $updateTextField('agentPhone', $agent->phone);
        $updateTextField('agentEmail', $agent->email);

        // Payment method checkbox
        $updateCheckbox('checkBoxOtherFormPayment', $notice->payment_other_means);

        // Service date information - just using current date for now, these should be updated when services actually happen
        $currentDate = Carbon::now()->format('F j, Y');
        $updateTextField('personalServiceDate', $currentDate);
        $updateTextField('postedServiceDate', $currentDate);
        $updateTextField('firstClassServiceDate', $currentDate);
        $updateTextField('addendumServiceDate', $currentDate);

        // Generate unique filename based on notice ID
        $fileName = 'notice_' . $notice->id . '_' . time() . '.json';
        $storagePath = 'notices/' . $fileName;

        // Make sure the notices directory exists
        Storage::makeDirectory('notices');

        // Store the generated JSON
        Storage::put($storagePath, json_encode($template, JSON_PRETTY_PRINT));

        return $storagePath;
    }
}
