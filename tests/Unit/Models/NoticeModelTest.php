<?php

namespace Tests\Unit;

use App\Models\Notice;
use App\Models\Tenant;
use App\Models\Account;
use App\Models\Agent;
use App\Models\User;
use App\Models\NoticeType;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NoticeModelTest extends TestCase
{
    use RefreshDatabase;

    protected $account;
    protected $user;
    protected $noticeType;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create the basic data needed for all tests
        $this->account = Account::factory()->create();
        $this->user = User::factory()->create();
        
        // Attach the user to the account through the pivot table
        $this->user->accounts()->attach($this->account->id, ['is_owner' => true]);
        
        $this->noticeType = NoticeType::factory()->create();
    }

    /**
     * Test that a notice can have multiple tenants.
     */
    public function test_notice_can_have_multiple_tenants(): void
    {
        // Create an agent
        $agent = Agent::factory()->create(['account_id' => $this->account->id]);
        
        // Create a notice with minimal properties - don't use the factory's afterCreating
        $notice = new Notice([
            'account_id' => $this->account->id,
            'user_id' => $this->user->id,
            'agent_id' => $agent->id,
            'notice_type_id' => $this->noticeType->id,
            'price' => 50.00,
            'past_due_rent' => 1000.00,
            'late_charges' => 100.00,
            'status' => Notice::STATUS_DRAFT,
        ]);
        $notice->save();
        
        // Create three tenants
        $tenants = Tenant::factory()->count(3)->create([
            'account_id' => $this->account->id
        ]);
        
        // Attach the tenants
        foreach ($tenants as $tenant) {
            $notice->tenants()->attach($tenant->id);
        }
        
        // Verify that the notice has three tenants
        $this->assertEquals(3, $notice->tenants()->count());
    }
    
    /**
     * Test that a tenant can be associated with multiple notices.
     */
    public function test_tenant_can_have_multiple_notices(): void
    {
        // Create a tenant
        $tenant = Tenant::factory()->create(['account_id' => $this->account->id]);
        
        // Create an agent
        $agent = Agent::factory()->create(['account_id' => $this->account->id]);
        
        // Create three notices
        $notices = [];
        for ($i = 0; $i < 3; $i++) {
            $notice = Notice::factory()->create([
                'account_id' => $this->account->id,
                'user_id' => $this->user->id,
                'agent_id' => $agent->id,
                'notice_type_id' => $this->noticeType->id,
            ]);
            
            // Attach the tenant to the notice
            $notice->tenants()->attach($tenant->id);
            
            $notices[] = $notice;
        }
        
        // Verify that the tenant has three notices
        $this->assertEquals(3, $tenant->notices()->count());
    }
}
