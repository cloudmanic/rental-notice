<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Illuminate\Support\Sleep;

class ExampleTest extends DuskTestCase
{
    /**
     * A basic browser test example. //->assertSee('Oregon');
     */
    public function testBasicExample(): void
    {
        // Set up CSV file
        $csvPath = storage_path('app/property_managers.csv');
        $csvFile = fopen($csvPath, 'w');

        // Write CSV headers
        fputcsv($csvFile, [
            'first_name',
            'last_name',
            'street_1',
            'street_2',
            'city',
            'state',
            'zip',
            'expiration',
            'license_number',
            'status',
            'company_name',
            'phone',
            'email'
        ]);

        $this->browse(function (Browser $browser) use (&$collected, $csvFile) {
            $browser->visit('https://orea.elicense.micropact.com/Lookup/LicenseLookup.aspx')
                // Set the type to property manager
                ->select('ctl00$MainContentPlaceHolder$ucLicenseLookup$ctl03$lbMultipleCredentialTypePrefix', '8')

                // Set the state to Oregon
                ->select('ctl00$MainContentPlaceHolder$ucLicenseLookup$ctl03$ddStates', 'OR')

                // Submit the search
                ->press('ctl00$MainContentPlaceHolder$ucLicenseLookup$btnLookup')
                ->waitFor('.modal-window-lookup-results')   // wait for results modal

                // Iterate pages
                ->pause(500); // let animation finish

            do {
                // grab all rows in the results table
                $rows = $browser->elements('#ctl00_MainContentPlaceHolder_ucLicenseLookup_gvSearchResults tbody tr');

                // Iterate over each row
                foreach ($rows as $idx => $row) {

                    // Click the first link in this row using JavaScript
                    $browser->script("
                        const links = document.querySelectorAll('#ctl00_MainContentPlaceHolder_ucLicenseLookup_gvSearchResults tbody tr:nth-child(" . ($idx + 1) . ") a');
                        if (links.length > 0) {
                            links[0].click();
                        }
                    ");

                    // Wait for the detail page or modal to load
                    $browser->pause(1000); // Wait for any navigation or modal to appear

                    // Get the first and last name from the detail page
                    $name = $browser->text('#Grid0 > tbody > tr > td:nth-child(1)');

                    // Split the name into parts
                    $nameParts = explode(' ', trim($name));

                    // Extract first name (always the first part)
                    $firstName = isset($nameParts[0]) ? ucfirst(strtolower($nameParts[0])) : '';

                    // Extract last name (last part if multiple words)
                    $lastName = '';

                    if (count($nameParts) > 1) {
                        // Get the last part as the last name, regardless of how many parts
                        $lastName = ucfirst(strtolower(end($nameParts)));
                    }

                    // Get he address from the detail page
                    $address = $browser->text('#Grid0 > tbody > tr > td:nth-child(3)');

                    // Parse the address
                    $addressLines = explode("\n", trim($address));

                    // Set the street address (first line)
                    $street1 = $addressLines[0] ?? '';
                    $street2 = '';

                    // Check if there are multiple address lines
                    if (count($addressLines) > 2) {
                        // If there are 3+ lines, the middle line(s) become street_2
                        $street2 = implode(' ', array_slice($addressLines, 1, -1));
                        $cityStateZip = end($addressLines);
                    } else if (count($addressLines) > 1) {
                        // If there are 2 lines, the last one is city/state/zip
                        $cityStateZip = end($addressLines);
                    } else {
                        // If there's only one line, assume it's all the street address
                        $cityStateZip = '';
                    }

                    // Parse city, state, zip from the last line
                    $cityStateZipParts = [];
                    if (!empty($cityStateZip)) {
                        // Match pattern: City, ST ZIP
                        preg_match('/([^,]+),\s*(\w{2})\s+(\d{5}(?:-\d{4})?)/', $cityStateZip, $cityStateZipParts);
                    }

                    $city = $cityStateZipParts[1] ?? '';
                    $state = $cityStateZipParts[2] ?? '';
                    $zip = $cityStateZipParts[3] ?? '';

                    // Get License Number
                    $licenseNumber = $browser->text('#Grid1 > tbody > tr > td:nth-child(1)');

                    // Get expire date
                    $expireDate = $browser->text('#Grid1 > tbody > tr > td:nth-child(3)');

                    // Get status
                    $status = $browser->text('#Grid1 > tbody > tr > td:nth-child(4)');
                    $status = ucfirst(strtolower($status));

                    // Get the company name
                    $companyName = $browser->text('#Grid2 > tbody > tr > td:nth-child(1)');

                    // Convert company name to proper case while keeping LLC uppercase
                    if (!empty($companyName)) {
                        // Convert to lowercase first
                        $companyName = strtolower($companyName);

                        // Split by spaces to handle words separately
                        $words = explode(' ', $companyName);

                        // Process each word
                        foreach ($words as &$word) {
                            // If word is 'llc', make it uppercase
                            if (strtolower($word) === 'llc') {
                                $word = 'LLC';
                            } else {
                                // Capitalize the first letter of other words
                                $word = ucfirst($word);
                            }
                        }

                        // Rejoin the words
                        $companyName = implode(' ', $words);
                    }

                    // Add to collected data
                    $collected[] = [
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                        'street_1' => $street1,
                        'street_2' => $street2,
                        'city' => $city,
                        'state' => $state,
                        'zip' => $zip,
                        'expiration' => $expireDate,
                        'license_number' => $licenseNumber,
                        'status' => $status,
                        'company_name' => $companyName,
                        'phone' => '',
                        'email' => '',
                    ];

                    // Write to CSV file as we go
                    fputcsv($csvFile, [
                        $firstName,
                        $lastName,
                        $street1,
                        $street2,
                        $city,
                        $state,
                        $zip,
                        $expireDate,
                        $licenseNumber,
                        $status,
                        $companyName,
                        '', // phone
                        ''  // email
                    ]);

                    // Random pause to avoid detection and rate limiting
                    $browser->pause(1000); //random_int(3000, 5000)
                }

                // // Remove duplicates from the $collected array using license_number as the unique key (not sure why this happens)
                // $uniqueCollected = [];
                // $licenseNumbers = [];

                // foreach ($collected as $item) {
                //     $licenseNumber = $item['license_number'];
                //     if (!in_array($licenseNumber, $licenseNumbers)) {
                //         $licenseNumbers[] = $licenseNumber;
                //         $uniqueCollected[] = $item;
                //     }
                // }

                // $collected = $uniqueCollected;

                //dd($collected, count($collected));

                // Check if there's a next page by finding the active list item and then its next sibling with an <a> tag
                $hasNext = $browser->script("
                    const activeLi = document.querySelector('#ctl00_MainContentPlaceHolder_ucLicenseLookup_gvSearchResults > tfoot > tr > td > ul > li.active');
                    if (activeLi && activeLi.nextElementSibling && activeLi.nextElementSibling.querySelector('a')) {
                        activeLi.nextElementSibling.querySelector('a').click();
                        return true;
                    }
                    return false;
                ")[0];

                // Wait for the page to load if we clicked to a next page
                if ($hasNext) {
                    $browser->pause(1500); // Wait for the page to refresh
                    $browser->waitFor('#ctl00_MainContentPlaceHolder_ucLicenseLookup_gvSearchResults');
                }
            } while ($hasNext);
        });

        // Close the CSV file
        fclose($csvFile);

        dd($csvPath);
    }
}
