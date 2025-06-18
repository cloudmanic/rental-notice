<?php

namespace Tests\Feature\Realtors;

use App\Models\RealtorList;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RealtorControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $superAdmin;

    private User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superAdmin = User::factory()->create([
            'type' => User::TYPE_SUPER_ADMIN,
        ]);

        $this->regularUser = User::factory()->create([
            'type' => User::TYPE_CONTRIBUTOR,
        ]);
    }

    /**
     * Test super admin can access export
     */
    public function test_super_admin_can_access_export()
    {
        $this->actingAs($this->superAdmin);

        // Create test realtor
        RealtorList::create([
            'email' => 'test@example.com',
            'full_name' => 'Test Realtor',
            'city' => 'Portland',
            'state' => 'OR',
        ]);

        $response = $this->get(route('realtors.export'));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $response->assertHeader('content-disposition');
    }

    /**
     * Test regular user cannot access export
     */
    public function test_regular_user_cannot_access_export()
    {
        $this->actingAs($this->regularUser);

        $response = $this->get(route('realtors.export'));
        $response->assertStatus(403);
    }

    /**
     * Test guest cannot access export
     */
    public function test_guest_cannot_access_export()
    {
        $response = $this->get(route('realtors.export'));
        $response->assertStatus(302); // Redirect to login
    }

    /**
     * Test export returns CSV with correct headers
     */
    public function test_export_returns_csv_with_correct_headers()
    {
        $this->actingAs($this->superAdmin);

        // Clear any existing realtors
        RealtorList::truncate();

        // Create test realtors
        RealtorList::create([
            'email' => 'john@example.com',
            'full_name' => 'John Doe',
            'office_name' => 'Test Realty',
            'city' => 'Portland',
            'state' => 'OR',
            'phone' => '503-555-1234',
            'license_number' => 'LIC123',
            'expiration_date' => '2026-12-31',
        ]);

        RealtorList::create([
            'email' => 'jane@example.com',
            'full_name' => 'Jane Smith',
            'office_name' => 'Smith Properties',
            'city' => 'Eugene',
            'state' => 'OR',
            'phone' => '541-555-5678',
            'license_number' => 'LIC456',
            'expiration_date' => '2025-06-30',
        ]);

        $response = $this->get(route('realtors.export'));

        $response->assertStatus(200);

        // Handle streamed response properly
        ob_start();
        $response->sendContent();
        $content = ob_get_clean();

        $lines = explode("\n", trim($content));

        // Check CSV headers (default columns)
        $expectedHeaders = [
            'Full Name',
            'Email',
            'Office Name',
            'City',
            'State',
            'Phone',
            'License Number',
            'License Expiration',
        ];

        $headerLine = str_getcsv($lines[0]);
        $this->assertEquals($expectedHeaders, $headerLine);

        // Check data rows exist
        $this->assertCount(3, $lines); // Header + 2 data rows
        $this->assertStringContainsString('John Doe', $content);
        $this->assertStringContainsString('jane@example.com', $content);
        $this->assertStringContainsString('Test Realty', $content);
        $this->assertStringContainsString('Portland', $content);
    }

    /**
     * Test export respects session column preferences
     */
    public function test_export_respects_session_column_preferences()
    {
        $this->actingAs($this->superAdmin);

        // Clear any existing realtors
        RealtorList::truncate();

        // Set custom columns in session
        $customColumns = ['full_name', 'email', 'mobile', 'agency'];
        session(['realtor_columns' => $customColumns]);

        // Create test realtor
        RealtorList::create([
            'email' => 'test@example.com',
            'full_name' => 'Test Realtor',
            'mobile' => '503-555-9999',
            'agency' => 'RE/MAX',
            'city' => 'Portland', // This should not appear in export
            'phone' => '503-555-1234', // This should not appear in export
        ]);

        $response = $this->get(route('realtors.export'));

        // Handle streamed response properly
        ob_start();
        $response->sendContent();
        $content = ob_get_clean();

        $lines = explode("\n", trim($content));

        // Check headers match session preferences
        $headerLine = str_getcsv($lines[0]);
        $this->assertEquals(['Full Name', 'Email', 'Mobile', 'Agency'], $headerLine);

        // Check that non-selected columns are not included
        $this->assertStringNotContainsString('City', $lines[0]);
        $this->assertStringNotContainsString('Phone', $lines[0]);

        // Check data includes selected columns
        $this->assertStringContainsString('Test Realtor', $content);
        $this->assertStringContainsString('503-555-9999', $content);
        $this->assertStringContainsString('RE/MAX', $content);
    }

    /**
     * Test export filename format
     */
    public function test_export_filename_format()
    {
        $this->actingAs($this->superAdmin);

        $response = $this->get(route('realtors.export'));

        $contentDisposition = $response->headers->get('content-disposition');
        $this->assertStringContainsString('attachment; filename="realtors_export_', $contentDisposition);
        $this->assertStringContainsString('.csv"', $contentDisposition);

        // Check filename contains date
        $today = date('Y-m-d');
        $this->assertStringContainsString($today, $contentDisposition);
    }

    /**
     * Test export handles large datasets
     */
    public function test_export_handles_large_datasets()
    {
        $this->actingAs($this->superAdmin);

        // Clear any existing realtors
        RealtorList::truncate();

        // Create 100 realtors (smaller number for faster test)
        $realtors = [];
        for ($i = 1; $i <= 100; $i++) {
            $realtors[] = [
                'email' => "realtor{$i}@example.com",
                'full_name' => "Realtor {$i}",
                'city' => 'Portland',
                'state' => 'OR',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        RealtorList::insert($realtors);

        $start = microtime(true);
        $response = $this->get(route('realtors.export'));
        $end = microtime(true);

        $response->assertStatus(200);

        // Should complete within reasonable time (5 seconds for 100 records)
        $this->assertLessThan(5, $end - $start, 'Export should complete within 5 seconds');

        // Handle streamed response properly
        ob_start();
        $response->sendContent();
        $content = ob_get_clean();

        $lines = explode("\n", trim($content));

        // Should have header + 100 data rows
        $this->assertEquals(101, count($lines));

        // Check first and last realtor are included
        $this->assertStringContainsString('Realtor 1', $content);
        $this->assertStringContainsString('Realtor 100', $content);
    }

    /**
     * Test export with empty dataset
     */
    public function test_export_with_empty_dataset()
    {
        $this->actingAs($this->superAdmin);

        // Clear any existing realtors
        RealtorList::truncate();

        $response = $this->get(route('realtors.export'));

        $response->assertStatus(200);

        // Handle streamed response properly
        ob_start();
        $response->sendContent();
        $content = ob_get_clean();

        $lines = explode("\n", trim($content));

        // Should only have header row
        $this->assertEquals(1, count($lines)); // header only

        // Check headers are present
        $headerLine = str_getcsv($lines[0]);
        $this->assertContains('Full Name', $headerLine);
        $this->assertContains('Email', $headerLine);
    }

    /**
     * Test export date formatting
     */
    public function test_export_date_formatting()
    {
        $this->actingAs($this->superAdmin);

        // Clear any existing realtors
        RealtorList::truncate();

        // Set session to include expiration_date
        session(['realtor_columns' => ['full_name', 'expiration_date']]);

        // Create realtor with expiration date
        RealtorList::create([
            'full_name' => 'Date Test Realtor',
            'expiration_date' => '2026-07-31',
        ]);

        $response = $this->get(route('realtors.export'));

        // Handle streamed response properly
        ob_start();
        $response->sendContent();
        $content = ob_get_clean();

        // Check date is formatted as m/d/Y
        $this->assertStringContainsString('07/31/2026', $content);
        $this->assertStringNotContainsString('2026-07-31', $content);
    }

    /**
     * Test export handles null values correctly
     */
    public function test_export_handles_null_values()
    {
        $this->actingAs($this->superAdmin);

        // Clear any existing realtors
        RealtorList::truncate();

        // Create realtor with some null values
        RealtorList::create([
            'email' => 'null-test@example.com',
            'full_name' => 'Null Test Realtor',
            'office_name' => null,
            'city' => 'Portland',
            'phone' => null,
            'mobile' => '503-555-1234',
        ]);

        $response = $this->get(route('realtors.export'));

        // Handle streamed response properly
        ob_start();
        $response->sendContent();
        $content = ob_get_clean();

        $lines = explode("\n", trim($content));

        // Parse the data row
        $dataRow = str_getcsv($lines[1]);

        $this->assertContains('Null Test Realtor', $dataRow);
        $this->assertContains('null-test@example.com', $dataRow);
        $this->assertContains('Portland', $dataRow);

        // Null values should be empty strings in CSV
        $nullOfficeIndex = array_search('Office Name', str_getcsv($lines[0]));
        $this->assertEquals('', $dataRow[$nullOfficeIndex]);
    }

    /**
     * Test export uses chunk processing for memory efficiency
     */
    public function test_export_memory_efficiency()
    {
        $this->actingAs($this->superAdmin);

        // Clear any existing realtors
        RealtorList::truncate();

        // Create many realtors to test chunking (smaller number for test speed)
        $realtors = [];
        for ($i = 1; $i <= 250; $i++) {
            $realtors[] = [
                'email' => "realtor{$i}@example.com",
                'full_name' => "Realtor {$i}",
                'city' => 'Portland',
                'state' => 'OR',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        RealtorList::insert($realtors);

        $initialMemory = memory_get_usage();

        $response = $this->get(route('realtors.export'));

        $finalMemory = memory_get_usage();

        $response->assertStatus(200);

        // Memory usage should not increase dramatically (less than 10MB for 250 records)
        $memoryIncrease = $finalMemory - $initialMemory;
        $this->assertLessThan(10 * 1024 * 1024, $memoryIncrease, 'Memory usage should not increase more than 10MB');

        // Verify all data is exported
        // Handle streamed response properly
        ob_start();
        $response->sendContent();
        $content = ob_get_clean();

        $this->assertStringContainsString('Realtor 1', $content);
        $this->assertStringContainsString('Realtor 250', $content);
    }

    /**
     * Test route is protected by superadmin middleware
     */
    public function test_route_protected_by_superadmin_middleware()
    {
        // Test route exists and is protected
        $this->assertNotNull(route('realtors.export'));

        // Create regular user account with different account
        $account = \App\Models\Account::factory()->create();
        $user = User::factory()->create([
            'type' => User::TYPE_CONTRIBUTOR,
        ]);

        $this->actingAs($user);

        $response = $this->get(route('realtors.export'));
        $response->assertStatus(403);
    }
}
