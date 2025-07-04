<?php

namespace Tests\Unit\Livewire\Notices;

use App\Livewire\Notices\Create;
use App\Models\Account;
use App\Models\Agent;
use App\Models\NoticeType;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CreateTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    private $account;

    private $noticeType10Day;

    private $noticeType13Day;

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

        // Create notice types
        $this->noticeType10Day = NoticeType::factory()->create([
            'name' => '10-Day Notice of Termination for Nonpayment of Rent',
            'price' => 15.00,
            'plan_date' => '2025-01-01',
        ]);

        $this->noticeType13Day = NoticeType::factory()->create([
            'name' => '13-Day Notice of Termination for Nonpayment of Rent',
            'price' => 15.00,
            'plan_date' => '2025-01-01',
        ]);

        // Create an agent
        Agent::factory()->create([
            'account_id' => $this->account->id,
        ]);

        // Create a tenant
        Tenant::factory()->create([
            'account_id' => $this->account->id,
        ]);
    }

    /**
     * Test that warning message appears before the 5th at before 1pm PST.
     *
     * @return void
     */
    public function test_warning_appears_before_5th_before_1pm_pst()
    {
        // Set date to 3rd of month at 11 AM PST
        Carbon::setTestNow(Carbon::parse('2025-01-03 11:00:00', 'America/Los_Angeles'));

        Livewire::actingAs($this->user)
            ->test(Create::class)
            ->assertSee('If you are serving notice to someone who is late this month, you need to wait until the 5th for a 13-day notice or the 8th for a 10-day notice.');
    }

    /**
     * Test that warning does not appear before the 5th after 1pm PST.
     *
     * @return void
     */
    public function test_warning_does_not_appear_before_5th_after_1pm_pst()
    {
        // Set date to 3rd of month at 2 PM PST
        Carbon::setTestNow(Carbon::parse('2025-01-03 14:00:00', 'America/Los_Angeles'));

        Livewire::actingAs($this->user)
            ->test(Create::class)
            ->assertDontSee('If you are serving notice to someone who is late this month');
    }

    /**
     * Test that warning appears between 5th and 8th for 10-day notice.
     *
     * @return void
     */
    public function test_warning_appears_between_5th_and_8th_for_10_day_notice()
    {
        // Set date to 6th of month at 11 AM PST
        Carbon::setTestNow(Carbon::parse('2025-01-06 11:00:00', 'America/Los_Angeles'));

        Livewire::actingAs($this->user)
            ->test(Create::class)
            ->set('notice.notice_type_id', $this->noticeType10Day->id)
            ->assertSee('If you are serving notice to someone who is late this month, you can only serve a 13-day notice until the 8th of the month.');
    }

    /**
     * Test that warning does not appear between 5th and 8th for 13-day notice.
     *
     * @return void
     */
    public function test_warning_does_not_appear_between_5th_and_8th_for_13_day_notice()
    {
        // Set date to 6th of month at 11 AM PST
        Carbon::setTestNow(Carbon::parse('2025-01-06 11:00:00', 'America/Los_Angeles'));

        Livewire::actingAs($this->user)
            ->test(Create::class)
            ->set('notice.notice_type_id', $this->noticeType13Day->id)
            ->assertDontSee('If you are serving notice to someone who is late this month, you can only serve a 13-day notice until the 8th of the month.');
    }

    /**
     * Test that no warning appears on or after the 8th.
     *
     * @return void
     */
    public function test_no_warning_appears_on_or_after_8th()
    {
        // Set date to 8th of month at 11 AM PST
        Carbon::setTestNow(Carbon::parse('2025-01-08 11:00:00', 'America/Los_Angeles'));

        Livewire::actingAs($this->user)
            ->test(Create::class)
            ->set('notice.notice_type_id', $this->noticeType10Day->id)
            ->assertDontSee('If you are serving notice to someone who is late this month');
    }

    /**
     * Test that updatedNoticeNoticeTypeId method triggers warning update.
     *
     * @return void
     */
    public function test_updated_notice_type_id_triggers_warning_update()
    {
        // Set date to 6th of month at 11 AM PST
        Carbon::setTestNow(Carbon::parse('2025-01-06 11:00:00', 'America/Los_Angeles'));

        $component = Livewire::actingAs($this->user)
            ->test(Create::class);

        // Initially no warning
        $component->assertDontSee('If you are serving notice to someone who is late this month, you can only serve a 13-day notice until the 8th of the month.');

        // Select 10-day notice - warning should appear
        $component->set('notice.notice_type_id', $this->noticeType10Day->id)
            ->assertSee('If you are serving notice to someone who is late this month, you can only serve a 13-day notice until the 8th of the month.');

        // Switch to 13-day notice - warning should disappear
        $component->set('notice.notice_type_id', $this->noticeType13Day->id)
            ->assertDontSee('If you are serving notice to someone who is late this month, you can only serve a 13-day notice until the 8th of the month.');
    }

    /**
     * Test warning message property updates correctly.
     *
     * @return void
     */
    public function test_warning_message_property_updates_correctly()
    {
        // Set date to 3rd of month at 11 AM PST
        Carbon::setTestNow(Carbon::parse('2025-01-03 11:00:00', 'America/Los_Angeles'));

        $component = Livewire::actingAs($this->user)
            ->test(Create::class);

        // Check that warningMessage property is set correctly
        $component->assertSet('warningMessage', 'If you are serving notice to someone who is late this month, you need to wait until the 5th for a 13-day notice or the 8th for a 10-day notice.');
    }

    /**
     * Test timezone handling for PST.
     *
     * @return void
     */
    public function test_timezone_handling_for_pst()
    {
        // Set date to 3rd at 12:59 PM PST (which is 20:59 UTC in January)
        Carbon::setTestNow(Carbon::parse('2025-01-03 20:59:00', 'UTC'));

        Livewire::actingAs($this->user)
            ->test(Create::class)
            ->assertSee('If you are serving notice to someone who is late this month');

        // Set date to 3rd at 1:01 PM PST (which is 21:01 UTC in January)
        Carbon::setTestNow(Carbon::parse('2025-01-03 21:01:00', 'UTC'));

        Livewire::actingAs($this->user)
            ->test(Create::class)
            ->assertDontSee('If you are serving notice to someone who is late this month');
    }

    /**
     * Test that users can add up to 6 tenants.
     *
     * @return void
     */
    public function test_can_add_up_to_six_tenants()
    {
        // Create 6 tenants
        $tenants = Tenant::factory()->count(6)->create([
            'account_id' => $this->account->id,
        ]);

        $component = Livewire::actingAs($this->user)
            ->test(Create::class);

        // Add each tenant
        foreach ($tenants as $tenant) {
            $component->call('addTenant', $tenant->id);
        }

        // Verify all 6 tenants are selected
        $component->assertCount('selectedTenants', 6);
    }

    /**
     * Test that users cannot add more than 6 tenants.
     *
     * @return void
     */
    public function test_cannot_add_more_than_six_tenants()
    {
        // Create 7 tenants
        $tenants = Tenant::factory()->count(7)->create([
            'account_id' => $this->account->id,
        ]);

        $component = Livewire::actingAs($this->user)
            ->test(Create::class);

        // Add first 6 tenants
        foreach ($tenants->take(6) as $tenant) {
            $component->call('addTenant', $tenant->id);
        }

        // Try to add 7th tenant - should be prevented
        $component->call('addTenant', $tenants->last()->id);

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
        // Create agent and tenants
        $agent = Agent::factory()->create(['account_id' => $this->account->id]);
        $tenants = Tenant::factory()->count(7)->create([
            'account_id' => $this->account->id,
        ]);

        $component = Livewire::actingAs($this->user)
            ->test(Create::class)
            ->set('notice.notice_type_id', $this->noticeType10Day->id)
            ->set('notice.agent_id', $agent->id)
            ->set('notice.past_due_rent', 1000)
            ->set('notice.late_charges', 50);

        // Manually set more than 6 tenants to test validation
        $selectedTenants = $tenants->map(function ($tenant) {
            return [
                'id' => $tenant->id,
                'name' => $tenant->full_name,
            ];
        })->toArray();

        $component->set('selectedTenants', $selectedTenants)
            ->call('createNotice')
            ->assertHasErrors(['selectedTenants' => 'max']);
    }

    protected function tearDown(): void
    {
        // Reset the test time
        Carbon::setTestNow();
        parent::tearDown();
    }
}
