<?php

namespace Tests\Unit\Services;

use App\Models\Account;
use App\Models\Agent;
use App\Models\Notice;
use App\Models\NoticeType;
use App\Models\Tenant;
use App\Models\User;
use App\Services\DateService;
use App\Services\NoticeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
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
        $formattedExpectedTotal = number_format($expectedTotal, 2);

        // Attach tenants to notice
        $notice->tenants()->attach([$tenant1->id, $tenant2->id]);

        // Set up the templates directory for the test
        $templateDir = base_path('templates');
        if (! file_exists($templateDir)) {
            mkdir($templateDir, 0777, true);
        }

        // Create a simple template file for testing if it doesn't exist
        $templatePath = base_path('templates/10-day-notice-template.json');
        if (! file_exists($templatePath)) {
            $templateContent = file_get_contents(base_path('templates/10-day-notice-template.json'));
            file_put_contents($templatePath, $templateContent);
        }

        // Call the service method
        $dateService = new DateService;
        $noticeService = new NoticeService($dateService);
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
        $this->assertEquals('123 Main Street, Apt 4B', $this->getFieldValue($textfields, 'streetAddress'));
        $this->assertEquals('', $this->getFieldValue($textfields, 'unitNumber'));
        $this->assertEquals('Portland', $this->getFieldValue($textfields, 'city'));
        $this->assertEquals('OR', $this->getFieldValue($textfields, 'state'));
        $this->assertEquals('97201', $this->getFieldValue($textfields, 'zip'));

        // Check financial information
        $this->assertEquals('1,250.00', $this->getFieldValue($textfields, 'pastDueRent'));
        $this->assertEquals('1,250.00', $this->getFieldValue($textfields, 'rentAmountDue'));
        $this->assertEquals('75.00', $this->getFieldValue($textfields, 'lateCharges'));
        $this->assertEquals($formattedExpectedTotal, $this->getFieldValue($textfields, 'totalOutstandingAmount'));

        // Check other charges
        $this->assertEquals('Legal fee', $this->getFieldValue($textfields, 'other1'));
        $this->assertEquals('Maintenance fee', $this->getFieldValue($textfields, 'other2'));
        $this->assertEquals('NSF fee', $this->getFieldValue($textfields, 'other3'));

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
        $dateService = new DateService;
        $noticeService = new NoticeService($dateService);
        $pdfPath = $noticeService->generatePdfNotice($notice);

        // Check if file exists
        Storage::assertExists($pdfPath);

        // Verify file extension is PDF
        $this->assertEquals('pdf', pathinfo($pdfPath, PATHINFO_EXTENSION));

        // Verify the file name format
        $this->assertStringContainsString('notice_'.$notice->id, $pdfPath);
    }

    #[Test]
    public function it_adds_draft_watermark_when_requested()
    {
        // Create the required models for testing
        $account = Account::factory()->create();
        $user = User::factory()->create();
        $user->accounts()->attach($account->id);
        $noticeType = NoticeType::factory()->create();
        $agent = Agent::factory()->create(['account_id' => $account->id]);
        $tenant = Tenant::factory()->create(['account_id' => $account->id]);

        $notice = Notice::factory()->create([
            'account_id' => $account->id,
            'user_id' => $user->id,
            'notice_type_id' => $noticeType->id,
            'agent_id' => $agent->id,
        ]);

        $notice->tenants()->attach([$tenant->id]);

        // Set up template files
        $this->setUpTemplateFiles();

        // Mock the Process class for both the form fill and watermark operations
        $this->mockPdfcpuProcess();

        // Reset the File::move mock to avoid conflicts with the one in mockPdfcpuProcess
        \Mockery::resetContainer();

        // Now set up the specific expectation for the watermark process
        File::shouldReceive('exists')->andReturn(true);
        File::shouldReceive('isDirectory')->andReturn(true);
        File::shouldReceive('makeDirectory')->andReturn(true);

        // Specific expectation for the watermark move operation
        File::shouldReceive('move')
            ->once()
            ->withArgs(function ($source, $dest) {
                return str_ends_with($source, '.temp.pdf') && ! str_ends_with($dest, '.temp.pdf');
            })
            ->andReturn(true);

        // Specific expectation for the flattened PDF move operation
        File::shouldReceive('move')
            ->once()
            ->withArgs(function ($source, $dest) {
                return str_ends_with($source, '.flattened.pdf') && ! str_ends_with($dest, '.flattened.pdf');
            })
            ->andReturn(true);

        // Additional expectation for the lockPdfForms move operation
        File::shouldReceive('move')
            ->once()
            ->withArgs(function ($source, $dest) {
                return str_ends_with($source, '.secured.pdf') && ! str_ends_with($dest, '.secured.pdf');
            })
            ->andReturn(true);

        // Call the service with watermark=true
        $dateService = new DateService;
        $noticeService = new NoticeService($dateService);
        $pdfPath = $noticeService->generatePdfNotice($notice, true);

        // Verify the path is correct
        $this->assertStringContainsString('notice_'.$notice->id, $pdfPath);
        $this->assertStringEndsWith('.pdf', $pdfPath);
    }

    #[Test]
    public function it_generates_json_shipping_form()
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
            'name' => 'Jane Smith',
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

        // Create notice without address fields
        $notice = Notice::factory()->create([
            'account_id' => $account->id,
            'user_id' => $user->id,
            'notice_type_id' => $noticeType->id,
            'agent_id' => $agent->id,
        ]);

        // Attach ONLY this tenant to notice
        $notice->tenants()->sync([$tenant->id]);

        // Set up the needed template files
        $this->setUpShippingFormTemplateFiles();

        // Call the service method
        $dateService = new DateService;
        $noticeService = new NoticeService($dateService);
        $storagePath = $noticeService->generateJsonShippingForm($notice);

        // Check if file exists
        Storage::assertExists($storagePath);

        // Read the generated file
        $jsonContent = Storage::get($storagePath);
        $generatedForm = json_decode($jsonContent, true);

        // Verify the JSON structure
        $this->assertIsArray($generatedForm);
        $this->assertArrayHasKey('forms', $generatedForm);
        $this->assertNotEmpty($generatedForm['forms']);

        // Get all textfields from the generated form
        $textfields = collect($generatedForm['forms'][0]['textfield']);

        // Check sender information (agent details)
        $this->assertEquals('Jane Smith', $this->getFieldValue($textfields, 'fromLine1'));
        $this->assertEquals('456 Property Lane', $this->getFieldValue($textfields, 'fromLine2'));
        $this->assertEquals('Suite 200', $this->getFieldValue($textfields, 'fromLine3'));
        $this->assertEquals('Portland, OR 97204', $this->getFieldValue($textfields, 'fromLine4'));

        // Check recipient information (tenant details)
        $this->assertEquals('John Doe', $this->getFieldValue($textfields, 'toLine1'));
        $this->assertEquals('123 Main Street', $this->getFieldValue($textfields, 'toLine2'));
        $this->assertEquals('Apt 4B', $this->getFieldValue($textfields, 'toLine3'));
        $this->assertEquals('Portland, OR 97201', $this->getFieldValue($textfields, 'toLine4'));

        // Verify the filename format
        $this->assertStringContainsString('shipping_'.$notice->id, $storagePath);
    }

    #[Test]
    public function it_generates_pdf_shipping_form()
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

        // Create notice without address fields
        $notice = Notice::factory()->create([
            'account_id' => $account->id,
            'user_id' => $user->id,
            'notice_type_id' => $noticeType->id,
            'agent_id' => $agent->id,
        ]);

        // Attach tenant to notice
        $notice->tenants()->sync([$tenant->id]);

        // Set up mock File operations
        // Reset all previous mocks to avoid conflicts
        \Mockery::resetContainer();

        // Set up the needed template files
        $this->setUpShippingFormTemplateFiles();

        // Set up additional directory mocks needed
        File::shouldReceive('isDirectory')->andReturn(true);
        File::shouldReceive('makeDirectory')->andReturn(true);
        File::shouldReceive('exists')->andReturn(true);
        File::shouldReceive('move')->andReturn(true);

        // Create a mock Process for testing
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

        // Call the service method
        $dateService = new DateService;
        $noticeService = new NoticeService($dateService);
        $pdfPath = $noticeService->generatePdfShippingForm($notice);

        // Check if file exists - use our own assertion since File::exists is mocked
        $this->assertStringContainsString('shipping_'.$notice->id, $pdfPath);
        $this->assertStringEndsWith('.pdf', $pdfPath);
    }

    /**
     * Set up the needed template files for testing
     */
    private function setUpTemplateFiles()
    {
        // Set up the templates directory
        $templateDir = base_path('templates');
        if (! file_exists($templateDir)) {
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
     * Set up the needed PS3817 shipping form template files for testing
     */
    private function setUpShippingFormTemplateFiles()
    {
        // Set up the templates directory
        $templateDir = base_path('templates');
        if (! file_exists($templateDir)) {
            mkdir($templateDir, 0777, true);
        }

        // Create a mock JSON template content for the shipping form
        $jsonTemplate = json_encode([
            'header' => [
                'source' => 'ps3817-form.pdf',
                'version' => 'pdfcpu v0.9.1 dev',
            ],
            'forms' => [
                [
                    'textfield' => [
                        ['name' => 'fromLine1', 'value' => ''],
                        ['name' => 'fromLine2', 'value' => ''],
                        ['name' => 'fromLine3', 'value' => ''],
                        ['name' => 'fromLine4', 'value' => ''],
                        ['name' => 'toLine1', 'value' => ''],
                        ['name' => 'toLine2', 'value' => ''],
                        ['name' => 'toLine3', 'value' => ''],
                        ['name' => 'toLine4', 'value' => ''],
                    ],
                ],
            ],
        ], JSON_PRETTY_PRINT);

        // Extend File::exists mock to cover shipping form templates
        File::shouldReceive('exists')
            ->andReturnUsing(function ($path) {
                // Return true for both 10-day and PS3817 template paths
                if (
                    strpos($path, 'templates/10-day-notice-template') !== false ||
                    strpos($path, 'templates/ps3817-form') !== false
                ) {
                    return true;
                }

                return file_exists($path);
            });

        // Extend File::get mock to return our PS3817 template JSON
        File::shouldReceive('get')
            ->andReturnUsing(function ($path) use ($jsonTemplate) {
                // Return JSON content for the PS3817 template
                if ($path === base_path('templates/ps3817-form.json')) {
                    return $jsonTemplate;
                }

                // For other templates, use the existing mock or real file_get_contents
                return file_get_contents($path);
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
                return pathinfo($path, PATHINFO_EXTENSION) === 'pdf' ||
                    str_ends_with($path, '.flattened.pdf') ||
                    file_exists($path);
            });

        // Mock File::move for both watermark, flattening and lockPdfForms processes
        File::shouldReceive('move')->andReturnUsing(function ($source, $dest) {
            // This will handle moving any temporary files to replace the original
            return true;
        });
    }

    #[Test]
    public function it_generates_certificate_of_mailing_pdf()
    {
        // Create account first
        $account = Account::factory()->create();

        // Create a user connected to the account
        $user = User::factory()->create();
        $user->accounts()->attach($account->id);

        // Create a notice type
        $noticeType = NoticeType::factory()->create(['name' => '10-day']);

        // Create an agent connected to the account
        $agent = Agent::factory()->create([
            'account_id' => $account->id,
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
            'address_1' => '456 Oak Avenue',
            'address_2' => null,
            'city' => 'Portland',
            'state' => 'OR',
            'zip' => '97202',
        ]);

        // Create notices
        $notice1 = Notice::factory()->create([
            'account_id' => $account->id,
            'user_id' => $user->id,
            'notice_type_id' => $noticeType->id,
            'agent_id' => $agent->id,
        ]);

        $notice2 = Notice::factory()->create([
            'account_id' => $account->id,
            'user_id' => $user->id,
            'notice_type_id' => $noticeType->id,
            'agent_id' => $agent->id,
        ]);

        // Attach tenants to notices
        $notice1->tenants()->attach([$tenant1->id]);
        $notice2->tenants()->attach([$tenant2->id]);

        // Mock the PDF facade
        $mockPdf = \Mockery::mock();
        $mockPdf->shouldReceive('output')->andReturn('PDF content');

        $this->app->bind('dompdf.wrapper', function () use ($mockPdf) {
            $wrapper = \Mockery::mock(\Barryvdh\DomPDF\ServiceProvider::class);
            $wrapper->shouldReceive('loadView')
                ->once()
                ->withArgs(function ($view, $data) {
                    return $view === 'pdfs.certificate-of-mailing' &&
                        isset($data['mailingDate']) &&
                        isset($data['noticeType']) &&
                        isset($data['tenantAddresses']) &&
                        isset($data['companyName']) &&
                        isset($data['companyAddress1']) &&
                        isset($data['companyAddress2']) &&
                        isset($data['companyCity']) &&
                        isset($data['companyState']) &&
                        isset($data['companyZip']) &&
                        isset($data['companyPhone']) &&
                        isset($data['companyEmail']) &&
                        isset($data['postOfficeName']) &&
                        isset($data['postOfficeAddress']) &&
                        $data['noticeType'] === '10-day' &&
                        count($data['tenantAddresses']) === 2;
                })
                ->andReturn($mockPdf);

            return $wrapper;
        });

        // Call the service method
        $dateService = new DateService;
        $noticeService = new NoticeService($dateService);
        $pdfPath = $noticeService->generateCertificateOfMailing([$notice1, $notice2]);

        // Check if file exists
        Storage::assertExists($pdfPath);

        // Verify the path contains expected structure
        $this->assertStringContainsString('notices/certificates/', $pdfPath);
        $this->assertStringContainsString('certificate_of_mailing_', $pdfPath);
        $this->assertStringEndsWith('.pdf', $pdfPath);

        // Verify the PDF content was saved
        $savedContent = Storage::get($pdfPath);
        $this->assertEquals('PDF content', $savedContent);
    }

    private function getFieldValue($textfields, $fieldName)
    {
        return $textfields->firstWhere('name', $fieldName)['value'];
    }
}
