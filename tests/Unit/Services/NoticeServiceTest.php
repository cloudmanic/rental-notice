<?php

namespace Tests\Unit\Services;

use App\Models\Account;
use App\Models\Agent;
use App\Models\Notice;
use App\Models\NoticeType;
use App\Models\Tenant;
use App\Models\User;
use App\Services\NoticeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Process\Process;
use Tests\TestCase;

class NoticeServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
    }

    #[Test]
    public function it_generates_json_notice_file()
    {
        // Create account first
        $account = Account::factory()->create();

        // Create a user connected to the account
        $user = User::factory()->create();
        $user->accounts()->attach($account->id);

        // Create a notice type
        $noticeType = NoticeType::factory()->create();

        // Create an agent connected to the account
        $agent = Agent::factory()->create([
            'account_id' => $account->id,
            'address_1' => '456 Property Lane',
            'address_2' => 'Suite 200',
            'city' => 'Portland',
            'state' => 'OR',
            'zip' => '97204',
            'phone' => '(503) 555-1234',
            'email' => 'agent@example.com',
        ]);

        // Create tenants connected to the account
        $tenant1 = Tenant::factory()->create([
            'account_id' => $account->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'address_1' => '123 Main Street',
            'address_2' => 'Apt 4B',
            'city' => 'Portland',
            'state' => 'OR',
            'zip' => '97201',
        ]);

        $tenant2 = Tenant::factory()->create([
            'account_id' => $account->id,
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'address_1' => '123 Main Street',
            'address_2' => 'Apt 4B',
            'city' => 'Portland',
            'state' => 'OR',
            'zip' => '97201',
        ]);

        // Create notice with all required relationships
        // We'll set specific values for all prices so we can calculate the expected total
        $pastDueRent = 1250.00;
        $lateCharges = 75.00;
        $other1Price = 100.00;
        $other2Price = 75.00;
        $other3Price = 35.00;

        $notice = Notice::factory()->create([
            'account_id' => $account->id,
            'user_id' => $user->id,
            'notice_type_id' => $noticeType->id,
            'agent_id' => $agent->id,
            'past_due_rent' => $pastDueRent,
            'late_charges' => $lateCharges,
            'other_1_title' => 'Legal fee',
            'other_1_price' => $other1Price,
            'other_2_title' => 'Maintenance fee',
            'other_2_price' => $other2Price,
            'other_3_title' => 'NSF fee',
            'other_3_price' => $other3Price,
            'other_4_title' => null,
            'other_4_price' => null,
            'other_5_title' => null,
            'other_5_price' => null,
            'payment_other_means' => true,
        ]);

        // Calculate expected total based on the getTotalChargesAttribute method in the Notice model
        $expectedTotal = $pastDueRent + $lateCharges + $other1Price + $other2Price + $other3Price;
        $formattedExpectedTotal = '$' . number_format($expectedTotal, 2);

        // Attach tenants to notice
        $notice->tenants()->attach([$tenant1->id, $tenant2->id]);

        // Set up the templates directory for the test
        $templateDir = base_path('templates');
        if (!file_exists($templateDir)) {
            mkdir($templateDir, 0777, true);
        }

        // Create a simple template file for testing if it doesn't exist
        $templatePath = base_path('templates/10-day-notice-template.json');
        if (!file_exists($templatePath)) {
            $templateContent = file_get_contents(base_path('templates/10-day-notice-template.json'));
            file_put_contents($templatePath, $templateContent);
        }

        // Call the service method
        $noticeService = new NoticeService();
        $storagePath = $noticeService->generateJsonNotice($notice);

        // Check if file exists
        Storage::assertExists($storagePath);

        // Read the generated file
        $jsonContent = Storage::get($storagePath);
        $generatedNotice = json_decode($jsonContent, true);

        // Verify the JSON structure
        $this->assertIsArray($generatedNotice);
        $this->assertArrayHasKey('forms', $generatedNotice);
        $this->assertNotEmpty($generatedNotice['forms']);

        // Get all textfields from the generated notice
        $textfields = collect($generatedNotice['forms'][0]['textfield']);

        // Check tenant information
        $this->assertEquals('John Doe', $this->getFieldValue($textfields, 'tenant1'));
        $this->assertEquals('Jane Smith', $this->getFieldValue($textfields, 'tenant2'));
        $this->assertEquals('123 Main Street', $this->getFieldValue($textfields, 'streetAddress'));
        $this->assertEquals('Apt 4B', $this->getFieldValue($textfields, 'unitNumber'));
        $this->assertEquals('Portland', $this->getFieldValue($textfields, 'city'));
        $this->assertEquals('OR', $this->getFieldValue($textfields, 'state'));
        $this->assertEquals('97201', $this->getFieldValue($textfields, 'zip'));

        // Check financial information
        $this->assertEquals('$1,250.00', $this->getFieldValue($textfields, 'pastDueRent'));
        $this->assertEquals('$1,250.00', $this->getFieldValue($textfields, 'rentAmountDue'));
        $this->assertEquals('$75.00', $this->getFieldValue($textfields, 'lateCharges'));
        $this->assertEquals($formattedExpectedTotal, $this->getFieldValue($textfields, 'totalOutstandingAmount'));

        // Check other charges
        $this->assertEquals('Legal fee: $100.00', $this->getFieldValue($textfields, 'other1'));
        $this->assertEquals('Maintenance fee: $75.00', $this->getFieldValue($textfields, 'other2'));
        $this->assertEquals('NSF fee: $35.00', $this->getFieldValue($textfields, 'other3'));

        // Check agent information
        $this->assertEquals('456 Property Lane', $this->getFieldValue($textfields, 'agentAddress1'));
        $this->assertEquals('Portland, OR 97204', $this->getFieldValue($textfields, 'agentAddress2'));
        $this->assertEquals('(503) 555-1234', $this->getFieldValue($textfields, 'agentPhone'));
        $this->assertEquals('agent@example.com', $this->getFieldValue($textfields, 'agentEmail'));

        // Check checkboxes
        $checkboxes = collect($generatedNotice['forms'][0]['checkbox']);
        $otherFormPayment = $checkboxes->firstWhere('name', 'checkBoxOtherFormPayment')['value'];
        $this->assertTrue($otherFormPayment);
    }

    #[Test]
    public function it_generates_pdf_notice_file()
    {
        // Create account first
        $account = Account::factory()->create();

        // Create a user connected to the account
        $user = User::factory()->create();
        $user->accounts()->attach($account->id);

        // Create a notice type
        $noticeType = NoticeType::factory()->create();

        // Create an agent connected to the account
        $agent = Agent::factory()->create([
            'account_id' => $account->id,
            'address_1' => '456 Property Lane',
            'address_2' => 'Suite 200',
            'city' => 'Portland',
            'state' => 'OR',
            'zip' => '97204',
            'phone' => '(503) 555-1234',
            'email' => 'agent@example.com',
        ]);

        // Create tenant connected to the account
        $tenant = Tenant::factory()->create([
            'account_id' => $account->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'address_1' => '123 Main Street',
            'address_2' => 'Apt 4B',
            'city' => 'Portland',
            'state' => 'OR',
            'zip' => '97201',
        ]);

        // Create notice
        $notice = Notice::factory()->create([
            'account_id' => $account->id,
            'user_id' => $user->id,
            'notice_type_id' => $noticeType->id,
            'agent_id' => $agent->id,
            'past_due_rent' => 1250.00,
            'late_charges' => 75.00,
            'other_1_title' => 'Legal fee',
            'other_1_price' => 100.00,
        ]);

        // Attach tenant to notice
        $notice->tenants()->attach([$tenant->id]);

        // Set up the needed template files
        $this->setUpTemplateFiles();

        // Create a mock Process for testing
        $this->mockPdfcpuProcess();

        // Call the service method
        $noticeService = new NoticeService();
        $pdfPath = $noticeService->generatePdfNotice($notice);

        // Check if file exists
        Storage::assertExists($pdfPath);

        // Verify file extension is PDF
        $this->assertEquals('pdf', pathinfo($pdfPath, PATHINFO_EXTENSION));

        // Verify the file name format
        $this->assertStringContainsString('notice_' . $notice->id, $pdfPath);
    }

    /**
     * Set up the needed template files for testing
     */
    private function setUpTemplateFiles()
    {
        // Set up the templates directory
        $templateDir = base_path('templates');
        if (!file_exists($templateDir)) {
            mkdir($templateDir, 0777, true);
        }

        // Create a mock JSON template content
        $jsonTemplate = json_encode([
            'header' => [
                'source' => '10-day-notice-template.pdf',
                'version' => 'pdfcpu v0.9.1 dev',
            ],
            'forms' => [
                [
                    'textfield' => [
                        ['name' => 'date', 'value' => ''],
                        ['name' => 'tenant1', 'value' => ''],
                        ['name' => 'tenant2', 'value' => ''],
                        ['name' => 'streetAddress', 'value' => ''],
                        ['name' => 'unitNumber', 'value' => ''],
                        ['name' => 'city', 'value' => ''],
                        ['name' => 'state', 'value' => ''],
                        ['name' => 'zip', 'value' => ''],
                        ['name' => 'pastDueRent', 'value' => ''],
                        ['name' => 'rentAmountDue', 'value' => ''],
                        ['name' => 'lateCharges', 'value' => ''],
                        ['name' => 'other1', 'value' => ''],
                        ['name' => 'other2', 'value' => ''],
                        ['name' => 'other3', 'value' => ''],
                        ['name' => 'other4', 'value' => ''],
                        ['name' => 'other5', 'value' => ''],
                        ['name' => 'totalOutstandingAmount', 'value' => ''],
                        ['name' => 'agentAddress1', 'value' => ''],
                        ['name' => 'agentAddress2', 'value' => ''],
                        ['name' => 'agentPhone', 'value' => ''],
                        ['name' => 'agentEmail', 'value' => ''],
                        ['name' => 'personalServiceDate', 'value' => ''],
                        ['name' => 'postedServiceDate', 'value' => ''],
                        ['name' => 'firstClassServiceDate', 'value' => ''],
                        ['name' => 'addendumServiceDate', 'value' => ''],
                    ],
                    'checkbox' => [
                        ['name' => 'checkBoxOtherFormPayment', 'value' => false],
                    ],
                ],
            ],
        ], JSON_PRETTY_PRINT);

        // Mock File::exists to return true for template paths
        File::shouldReceive('exists')
            ->andReturnUsing(function ($path) {
                // Return true for template paths, otherwise use the real file_exists
                if (strpos($path, 'templates/10-day-notice-template') !== false) {
                    return true;
                }
                return file_exists($path);
            });

        // Mock File::get to return our template JSON
        File::shouldReceive('get')
            ->andReturnUsing(function ($path) use ($jsonTemplate) {
                // Return JSON content for the template
                if ($path === base_path('templates/10-day-notice-template.json')) {
                    return $jsonTemplate;
                }
                // Use real file_get_contents for other files
                return file_get_contents($path);
            });
            
        // Mock File::isDirectory to work with our fake directories
        File::shouldReceive('isDirectory')
            ->andReturnUsing(function ($path) {
                // For test paths, return true
                if (strpos($path, 'storage') !== false) {
                    return true;
                }
                return is_dir($path);
            });
            
        // Mock File::makeDirectory to not do anything in tests
        File::shouldReceive('makeDirectory')
            ->andReturnUsing(function ($path, $mode, $recursive, $force = false) {
                return true;
            });
    }

    /**
     * Mock the pdfcpu Process for testing
     */
    private function mockPdfcpuProcess()
    {
        // Create a mock of the Process class
        $process = $this->getMockBuilder(Process::class)
            ->disableOriginalConstructor()
            ->getMock();

        // Mock the run method to return exit code 0 (success)
        $process->method('run')->willReturn(0);

        // Mock the isSuccessful method to return true
        $process->method('isSuccessful')->willReturn(true);

        // Replace the real Process with our mock
        $this->app->bind(Process::class, function ($app) use ($process) {
            return $process;
        });

        // Make sure the PDF "exists" for the test
        File::shouldReceive('exists')
            ->andReturnUsing(function ($path) {
                // Return true for PDF files to simulate they were created
                return pathinfo($path, PATHINFO_EXTENSION) === 'pdf' || file_exists($path);
            });
    }

    private function getFieldValue($textfields, $fieldName)
    {
        return $textfields->firstWhere('name', $fieldName)['value'];
    }
}
