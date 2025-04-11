<?php

namespace Tests\Feature\Controllers;

use App\Models\Account;
use App\Models\Agent;
use App\Models\Notice;
use App\Models\NoticeType;
use App\Models\Tenant;
use App\Models\User;
use App\Services\NoticeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Mockery;
use Tests\TestCase;

class NoticeControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
    }

    public function test_authorized_user_can_generate_pdf()
    {
        // Create an account
        $account = Account::factory()->create();

        // Create a user
        $user = User::factory()->create();
        $user->accounts()->attach($account->id);

        // Create a notice type
        $noticeType = NoticeType::factory()->create();

        // Create an agent
        $agent = Agent::factory()->create([
            'account_id' => $account->id,
        ]);

        // Create a tenant
        $tenant = Tenant::factory()->create([
            'account_id' => $account->id,
        ]);

        // Create a notice
        $notice = Notice::factory()->create([
            'account_id' => $account->id,
            'user_id' => $user->id,
            'agent_id' => $agent->id,
            'notice_type_id' => $noticeType->id,
        ]);

        // Attach the tenant to the notice
        $notice->tenants()->attach([$tenant->id]);

        // Mock the NoticeService to avoid actually generating the PDF
        $mockService = Mockery::mock(NoticeService::class);

        // The PDF path we expect the service to return
        $pdfPath = 'notices/notice_' . $notice->id . '_' . time() . '.pdf';

        // Set up the mock to return our fake PDF path
        $mockService->shouldReceive('generatePdfNotice')
            ->once()
            ->with(Mockery::on(function ($arg) use ($notice) {
                return $arg->id === $notice->id;
            }), true) // Add the second parameter 'true' for watermarked
            ->andReturn($pdfPath);

        // Create a test PDF file
        Storage::put($pdfPath, 'Fake PDF content');

        // Replace the real service with our mock
        $this->app->instance(NoticeService::class, $mockService);

        // Mock File::exists to return true for our PDF
        File::shouldReceive('exists')
            ->andReturn(true);

        // Act as the authorized user
        $this->actingAs($user);

        // Make the request
        $response = $this->get(route('notices.pdf', $notice));

        // Assert successful response
        $response->assertSuccessful();

        // Assert correct content type for PDF
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_unauthorized_user_cannot_generate_pdf()
    {
        // Create two separate accounts
        $account1 = Account::factory()->create();
        $account2 = Account::factory()->create();

        // Create users for each account
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $user1->accounts()->attach($account1->id);
        $user2->accounts()->attach($account2->id);

        // Create a notice type
        $noticeType = NoticeType::factory()->create();

        // Create an agent
        $agent = Agent::factory()->create([
            'account_id' => $account1->id,
        ]);

        // Create a notice for account 1
        $notice = Notice::factory()->create([
            'account_id' => $account1->id,
            'user_id' => $user1->id,
            'agent_id' => $agent->id,
            'notice_type_id' => $noticeType->id,
        ]);

        // Act as user from account 2 (unauthorized)
        $this->actingAs($user2);

        // Make the request
        $response = $this->get(route('notices.pdf', $notice));

        // Assert forbidden response
        $response->assertForbidden();
    }

    public function test_authorized_user_can_generate_complete_pdf_package()
    {
        // Create an account
        $account = Account::factory()->create();

        // Create a user
        $user = User::factory()->create();
        $user->accounts()->attach($account->id);

        // Create a notice type
        $noticeType = NoticeType::factory()->create();

        // Create an agent
        $agent = Agent::factory()->create([
            'account_id' => $account->id,
        ]);

        // Create a tenant
        $tenant = Tenant::factory()->create([
            'account_id' => $account->id,
        ]);

        // Create a notice
        $notice = Notice::factory()->create([
            'account_id' => $account->id,
            'user_id' => $user->id,
            'agent_id' => $agent->id,
            'notice_type_id' => $noticeType->id,
        ]);

        // Attach the tenant to the notice
        $notice->tenants()->attach([$tenant->id]);

        // Mock the NoticeService to avoid actually generating the PDF
        $mockService = Mockery::mock(NoticeService::class);

        // The PDF path we expect the service to return
        $pdfPath = 'notices/complete_package_' . $notice->id . '_' . time() . '.pdf';

        // Set up the mock to return our fake PDF path
        $mockService->shouldReceive('generateCompletePdfPackage')
            ->once()
            ->with(Mockery::on(function ($arg) use ($notice) {
                return $arg->id === $notice->id;
            }), true) // Add the second parameter 'true' for watermarked
            ->andReturn($pdfPath);

        // Create a test PDF file
        Storage::put($pdfPath, 'Fake complete PDF package content');

        // Replace the real service with our mock
        $this->app->instance(NoticeService::class, $mockService);

        // Mock File::exists to return true for our PDF
        File::shouldReceive('exists')
            ->andReturn(true);

        // Act as the authorized user
        $this->actingAs($user);

        // Make the request
        $response = $this->get(route('notices.complete-package', $notice));

        // Assert successful response
        $response->assertSuccessful();

        // Assert correct content type for PDF
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_unauthorized_user_cannot_generate_complete_pdf_package()
    {
        // Create two separate accounts
        $account1 = Account::factory()->create();
        $account2 = Account::factory()->create();

        // Create users for each account
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $user1->accounts()->attach($account1->id);
        $user2->accounts()->attach($account2->id);

        // Create a notice type
        $noticeType = NoticeType::factory()->create();

        // Create an agent
        $agent = Agent::factory()->create([
            'account_id' => $account1->id,
        ]);

        // Create a notice for account 1
        $notice = Notice::factory()->create([
            'account_id' => $account1->id,
            'user_id' => $user1->id,
            'agent_id' => $agent->id,
            'notice_type_id' => $noticeType->id,
        ]);

        // Act as user from account 2 (unauthorized)
        $this->actingAs($user2);

        // Make the request
        $response = $this->get(route('notices.complete-package', $notice));

        // Assert forbidden response
        $response->assertForbidden();
    }
}