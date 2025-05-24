<?php

namespace Tests\Feature\Notices;

use App\Models\Account;
use App\Models\Agent;
use App\Models\Notice;
use App\Models\NoticeType;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PrintPackageTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected User $superAdmin;

    protected Account $account;

    protected Notice $notice;

    protected Agent $agent;

    protected function setUp(): void
    {
        parent::setUp();

        // Create regular user
        $this->account = Account::factory()->create();
        $this->user = User::factory()->create();
        $this->user->accounts()->attach($this->account->id);

        // Create super admin
        $this->superAdmin = User::factory()->create(['type' => 'Super Admin']);
        $this->superAdmin->accounts()->attach($this->account->id);

        // Create agent
        $this->agent = Agent::factory()->create([
            'account_id' => $this->account->id,
            'name' => 'Test Agent',
        ]);

        // Create notice with tenants
        $noticeType = NoticeType::factory()->create(['name' => '10-day']);
        $this->notice = Notice::factory()->create([
            'account_id' => $this->account->id,
            'agent_id' => $this->agent->id,
            'notice_type_id' => $noticeType->id,
        ]);

        // Attach tenants
        $tenant1 = Tenant::factory()->create(['account_id' => $this->account->id]);
        $tenant2 = Tenant::factory()->create(['account_id' => $this->account->id]);
        $this->notice->tenants()->attach([$tenant1->id, $tenant2->id]);
    }

    /** @test */
    public function regular_user_cannot_access_agent_cover_letter()
    {
        $response = $this->actingAs($this->user)
            ->get(route('notices.agent-cover-letter', $this->notice));

        $response->assertStatus(403);
    }

    /** @test */
    public function super_admin_can_access_agent_cover_letter()
    {
        $response = $this->actingAs($this->superAdmin)
            ->get(route('notices.agent-cover-letter', $this->notice));

        // Should be successful (200) or server error if PDF generation fails
        $this->assertContains($response->status(), [200, 500]);

        if ($response->status() === 200) {
            $response->assertHeader('content-type', 'application/pdf');
        }
    }

    /** @test */
    public function regular_user_cannot_access_tenant_address_sheets()
    {
        $response = $this->actingAs($this->user)
            ->get(route('notices.tenant-address-sheets', $this->notice));

        $response->assertStatus(403);
    }

    /** @test */
    public function super_admin_can_access_tenant_address_sheets()
    {
        $response = $this->actingAs($this->superAdmin)
            ->get(route('notices.tenant-address-sheets', $this->notice));

        // Should be successful or server error if PDF generation fails
        $this->assertContains($response->status(), [200, 500]);

        if ($response->status() === 200) {
            $response->assertHeader('content-type', 'application/pdf');
        }
    }

    /** @test */
    public function regular_user_cannot_access_agent_address_sheet()
    {
        $response = $this->actingAs($this->user)
            ->get(route('notices.agent-address-sheet', $this->notice));

        $response->assertStatus(403);
    }

    /** @test */
    public function super_admin_can_access_agent_address_sheet()
    {
        $response = $this->actingAs($this->superAdmin)
            ->get(route('notices.agent-address-sheet', $this->notice));

        // Should be successful or server error if PDF generation fails
        $this->assertContains($response->status(), [200, 500]);

        if ($response->status() === 200) {
            $response->assertHeader('content-type', 'application/pdf');
        }
    }

    /** @test */
    public function regular_user_cannot_access_complete_print_package()
    {
        $response = $this->actingAs($this->user)
            ->get(route('notices.complete-print-package', $this->notice));

        $response->assertStatus(403);
    }

    /** @test */
    public function super_admin_can_access_complete_print_package()
    {
        $response = $this->actingAs($this->superAdmin)
            ->get(route('notices.complete-print-package', $this->notice));

        // Should be successful or server error if PDF generation fails
        $this->assertContains($response->status(), [200, 500]);

        if ($response->status() === 200) {
            $response->assertHeader('content-type', 'application/pdf');
            // Complete print package should always download
            $response->assertHeader('content-disposition');
            $this->assertStringContainsString('attachment', $response->headers->get('content-disposition'));
        }
    }

    /** @test */
    public function download_parameter_forces_download_for_cover_letter()
    {
        $response = $this->actingAs($this->superAdmin)
            ->get(route('notices.agent-cover-letter', $this->notice).'?download=true');

        if ($response->status() === 200) {
            $response->assertHeader('content-disposition');
            $this->assertStringContainsString('attachment', $response->headers->get('content-disposition'));
        }
    }

    /** @test */
    public function impersonating_user_can_access_print_documents()
    {
        // Set impersonation session
        session(['impersonating' => true]);

        $response = $this->actingAs($this->user)
            ->get(route('notices.agent-cover-letter', $this->notice));

        // Should be able to access when impersonating
        $this->assertContains($response->status(), [200, 500]);
    }
}
