<?php

namespace App\Http\Controllers;

use App\Models\RealtorList;
use App\Models\User;
use Illuminate\Http\Request;

class RealtorController extends Controller
{
    /**
     * Export realtors to CSV
     */
    public function export(Request $request)
    {
        // Check if user is super admin
        if (auth()->user()->type !== User::TYPE_SUPER_ADMIN) {
            abort(403);
        }

        // Get selected columns from session
        $selectedColumns = session('realtor_columns', [
            'full_name',
            'email',
            'office_name',
            'city',
            'state',
            'phone',
            'license_number',
            'expiration_date',
        ]);

        // Available columns for labels
        $availableColumns = [
            'csv_id' => 'ID',
            'full_name' => 'Full Name',
            'email' => 'Email',
            'office_name' => 'Office Name',
            'city' => 'City',
            'state' => 'State',
            'county' => 'County',
            'phone' => 'Phone',
            'mobile' => 'Mobile',
            'license_type' => 'License Type',
            'license_number' => 'License Number',
            'expiration_date' => 'License Expiration',
            'association' => 'Association',
            'agency' => 'Agency',
            'listings' => 'Listings',
            'listings_volume' => 'Listings Volume',
            'sold' => 'Sold',
            'sold_volume' => 'Sold Volume',
            'email_status' => 'Email Status',
        ];

        $filename = 'realtors_export_'.date('Y-m-d_His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];

        $callback = function () use ($selectedColumns, $availableColumns) {
            $file = fopen('php://output', 'w');

            // Add headers
            $headers = [];
            foreach ($selectedColumns as $column) {
                $headers[] = $availableColumns[$column];
            }
            fputcsv($file, $headers);

            // Add data
            RealtorList::orderBy('full_name')->chunk(1000, function ($realtors) use ($file, $selectedColumns) {
                foreach ($realtors as $realtor) {
                    $row = [];
                    foreach ($selectedColumns as $column) {
                        $value = $realtor->$column;
                        if ($column === 'expiration_date' && $value) {
                            $value = $value->format('m/d/Y');
                        }
                        $row[] = $value;
                    }
                    fputcsv($file, $row);
                }
            });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
