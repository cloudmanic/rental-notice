<?php

namespace App\Console\Commands;

use App\Models\RealtorList;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use League\Csv\Reader;

class ImportRealtorCsv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:realtors {file : The path to the CSV file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import realtor data from CSV file into realtor_list table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filePath = $this->argument('file');

        if (! file_exists($filePath)) {
            $this->error("File not found: {$filePath}");

            return 1;
        }

        $this->info("Starting import from: {$filePath}");

        try {
            $csv = Reader::createFromPath($filePath, 'r');
            $csv->setHeaderOffset(0);

            $records = $csv->getRecords();
            $count = 0;
            $batchSize = 1000;
            $batch = [];

            DB::beginTransaction();

            foreach ($records as $record) {
                $batch[] = [
                    'csv_id' => $record['id'] ?? null,
                    'email' => $record['Email'] ?? null,
                    'full_name' => $record['Full Name'] ?? null,
                    'first_name' => $record['First Name'] ?? null,
                    'middle_name' => $record['Middle Name'] ?? null,
                    'last_name' => $record['Last Name'] ?? null,
                    'suffix' => $record['Suffix'] ?? null,
                    'office_name' => $record['Office Name'] ?? null,
                    'address1' => $record['Address1'] ?? null,
                    'address2' => $record['Address2'] ?? null,
                    'city' => $record['City'] ?? null,
                    'state' => $record['State'] ?? null,
                    'zip' => $record['Zip'] ?? null,
                    'county' => $record['County'] ?? null,
                    'phone' => $record['Phone'] ?? null,
                    'fax' => $record['Fax'] ?? null,
                    'mobile' => $record['Mobile'] ?? null,
                    'license_type' => $record['License Type'] ?? null,
                    'license_number' => $record['License Number'] ?? null,
                    'original_issue_date' => $this->parseDate($record['Original Issue Date'] ?? null),
                    'expiration_date' => $this->parseDate($record['Expiration Date'] ?? null),
                    'association' => $record['Association'] ?? null,
                    'agency' => $record['Agency'] ?? null,
                    'listings' => $this->parseInteger($record['Listings'] ?? null),
                    'listings_volume' => $this->parseDecimal($record['Listings Volume'] ?? null),
                    'sold' => $this->parseInteger($record['Sold'] ?? null),
                    'sold_volume' => $this->parseDecimal($record['Sold Volume'] ?? null),
                    'email_status' => $record['Email Status'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $count++;

                if (count($batch) >= $batchSize) {
                    RealtorList::insert($batch);
                    $batch = [];
                    $this->info("Imported {$count} records...");
                }
            }

            // Insert remaining records
            if (! empty($batch)) {
                RealtorList::insert($batch);
            }

            DB::commit();

            $this->info("Import completed successfully! Total records imported: {$count}");

            return 0;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Import failed: '.$e->getMessage());

            return 1;
        }
    }

    /**
     * Parse date string to proper format or return null.
     */
    private function parseDate($dateString)
    {
        if (empty($dateString) || $dateString === '') {
            return null;
        }

        try {
            return \Carbon\Carbon::parse($dateString)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Parse integer value or return null.
     */
    private function parseInteger($value)
    {
        if ($value === '' || $value === null) {
            return null;
        }

        return (int) $value;
    }

    /**
     * Parse decimal value or return null.
     */
    private function parseDecimal($value)
    {
        if ($value === '' || $value === null) {
            return null;
        }

        return (float) str_replace(',', '', $value);
    }
}
