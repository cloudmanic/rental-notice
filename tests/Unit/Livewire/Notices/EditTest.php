<?php

namespace Tests\Unit\Livewire\Notices;

use App\Livewire\Notices\Edit;
use App\Models\Account;
use App\Models\Agent;
use App\Models\Notice;
use App\Models\NoticeType;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class EditTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    private $account;

    private $notice;

    protected function setUp(): void
    {
        parent::setUp();

        // Create account
        $this->account = Account::factory()->create([
            'notice_type_plan_date' => '2025-01-01',
        ]);

        // Create user and attach to account
        $this->user = User::factory()->create();
        $this->user->accounts()->attach($this->account->id, ['is_owner' => true]);

        // Create notice type
        $noticeType = NoticeType::factory()->create([
            'name' => '10-Day Notice of Termination for Nonpayment of Rent',
            'price' => 15.00,
            'plan_date' => '2025-01-01',
        ]);

        // Create agent
        $agent = Agent::factory()->create(['account_id' => $this->account->id]);

        // Create tenants
        $tenants = Tenant::factory()->count(2)->create([
            'account_id' => $this->account->id,
        ]);

        // Create notice
        $this->notice = Notice::factory()->create([
            'account_id' => $this->account->id,
            'user_id' => $this->user->id,
            'notice_type_id' => $noticeType->id,
            'agent_id' => $agent->id,
            'status' => Notice::STATUS_PENDING_PAYMENT,
        ]);

        // Attach tenants to notice
        $this->notice->tenants()->attach($tenants->pluck('id'));
    }

    /**
     * Test that users can add up to 6 tenants total.
     *
     * @return void
     */
    public function test_can_add_up_to_six_tenants_total()
    {
        // Create 4 additional tenants (notice already has 2)
        $additionalTenants = Tenant::factory()->count(4)->create([
            'account_id' => $this->account->id,
        ]);

        $component = Livewire::actingAs($this->user)
            ->test(Edit::class, ['notice' => $this->notice]);

        // Add the 4 additional tenants
        foreach ($additionalTenants as $tenant) {
            $component->call('addTenant', $tenant->id);
        }

        // Verify all 6 tenants are selected
        $component->assertCount('selectedTenants', 6);
    }

    /**
     * Test that users cannot add more than 6 tenants total.
     *
     * @return void
     */
    public function test_cannot_add_more_than_six_tenants_total()
    {
        // Create 5 additional tenants (notice already has 2, so this would be 7 total)
        $additionalTenants = Tenant::factory()->count(5)->create([
            'account_id' => $this->account->id,
        ]);

        $component = Livewire::actingAs($this->user)
            ->test(Edit::class, ['notice' => $this->notice]);

        // Add the first 4 tenants (bringing total to 6)
        foreach ($additionalTenants->take(4) as $tenant) {
            $component->call('addTenant', $tenant->id);
        }

        // Try to add 7th tenant - should be prevented
        $component->call('addTenant', $additionalTenants->last()->id);

        // Verify only 6 tenants are selected
        $component->assertCount('selectedTenants', 6);
    }

    /**
     * Test validation fails when submitting with more than 6 tenants.
     *
     * @return void
     */
    public function test_validation_fails_with_more_than_six_tenants()
    {
        // Create 5 additional tenants (7 total with existing 2)
        $additionalTenants = Tenant::factory()->count(5)->create([
            'account_id' => $this->account->id,
        ]);

        $component = Livewire::actingAs($this->user)
            ->test(Edit::class, ['notice' => $this->notice]);

        // Manually set more than 6 tenants to test validation
        $allTenants = $this->notice->tenants->concat($additionalTenants);
        $selectedTenants = $allTenants->map(function ($tenant) {
            return [
                'id' => $tenant->id,
                'name' => $tenant->full_name,
            ];
        })->toArray();

        $component->set('selectedTenants', $selectedTenants)
            ->call('updateNotice')
            ->assertHasErrors(['selectedTenants' => 'max']);
    }
}