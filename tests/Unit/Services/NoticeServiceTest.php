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
use PHPUnit\Framework\Attributes\Test;
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

    private function getFieldValue($textfields, $fieldName)
    {
        return $textfields->firstWhere('name', $fieldName)['value'];
    }
}
