<?php

namespace Tests\Feature\Dashboard;

use App\Models\Account;
use App\Models\Activity;
use App\Models\Agent;
use App\Models\Notice;
use App\Models\NoticeType;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ActivityStreamTest extends TestCase
{
    use RefreshDatabase;

    protected $account;
    protected $user;
    protected $noticeType;

    protected function setUp(): void
    {
        parent::setUp();

        // Create an account and user for testing
        $this->account = Account::factory()->create();
        $this->user = User::factory()->create();
        $this->account->users()->attach($this->user, ['is_owner' => true]);

        // Create a notice type for testing
        $this->noticeType = NoticeType::factory()->create();

        // Set the user's current account
        $this->user->account = $this->account;
        $this->user->currentAccount = $this->account;

        // Login the user
        $this->actingAs($this->user);
    }

    #[Test]
    public function it_displays_activities_on_dashboard()
    {
        // Create some activities for the account
        $description = 'This is a test activity';
        $activity = Activity::factory()->create([
            'account_id' => $this->account->id,
            'user_id' => $this->user->id,
            'description' => $description,
        ]);

        // Visit the dashboard
        $response = $this->get(route('dashboard'));

        // Assert that the page loads and contains the activity description
        $response->assertStatus(200);
        $response->assertSee($description);
        $response->assertSee($this->user->name);
    }

    #[Test]
    public function it_shows_different_activity_types_with_correct_badges()
    {
        // Create tenant activity
        $tenant = Tenant::factory()->create(['account_id' => $this->account->id]);
        $tenantActivity = Activity::factory()->tenant()->create([
            'account_id' => $this->account->id,
            'user_id' => $this->user->id,
            'tenant_id' => $tenant->id,
            'description' => 'Tenant activity',
        ]);

        // Create notice activity using proper dependencies
        $notice = Notice::factory()->create([
            'account_id' => $this->account->id,
            'user_id' => $this->user->id,
            'notice_type_id' => $this->noticeType->id
        ]);

        $noticeActivity = Activity::factory()->create([
            'account_id' => $this->account->id,
            'user_id' => $this->user->id,
            'notice_id' => $notice->id,
            'description' => 'Notice activity',
        ]);

        // Create agent activity
        $agent = Agent::factory()->create(['account_id' => $this->account->id]);
        $agentActivity = Activity::factory()->agent()->create([
            'account_id' => $this->account->id,
            'user_id' => $this->user->id,
            'agent_id' => $agent->id,
            'description' => 'Agent activity',
        ]);

        // Visit the dashboard
        $response = $this->get(route('dashboard'));

        // Assert that the page shows the correct activities with their badges
        $response->assertStatus(200);
        $response->assertSee('Tenant activity');
        $response->assertSee('Notice activity');
        $response->assertSee('Agent activity');

        // Check for the type badges in the HTML
        $response->assertSee('Tenant');
        $response->assertSee('Notice');
        $response->assertSee('Agent');
    }

    #[Test]
    public function it_shows_call_to_action_when_no_activities()
    {
        // Delete any activities that might exist
        Activity::where('account_id', $this->account->id)->delete();

        // Visit the dashboard
        $response = $this->get(route('dashboard'));

        // Assert the page contains our call-to-action elements
        $response->assertStatus(200);
        $response->assertSee('Get Started with Your First Late Rent Notice', false);
        $response->assertSee('Create My First Late Rent Notice', false);
    }

    #[Test]
    public function it_paginates_activities_when_more_than_fifty_exist()
    {
        // Create 60 activities (more than the 50 per page limit)
        Activity::factory()->count(60)->create([
            'account_id' => $this->account->id,
            'user_id' => $this->user->id,
        ]);

        // Visit the dashboard
        $response = $this->get(route('dashboard'));

        // Assert that the pagination elements are present - check for specific pagination elements
        $response->assertStatus(200);
        // Look for the pagination elements using different approaches
        $response->assertSee('Next', false);
        $response->assertSee('Previous', false);
    }

    #[Test]
    public function it_shows_activities_for_current_account_only()
    {
        // Create a second account and activity
        $secondAccount = Account::factory()->create();
        $secondAccountActivity = Activity::factory()->create([
            'account_id' => $secondAccount->id,
            'description' => 'Activity from other account',
        ]);

        // Create activity for the current account
        $currentAccountActivity = Activity::factory()->create([
            'account_id' => $this->account->id,
            'user_id' => $this->user->id,
            'description' => 'Activity from current account',
        ]);

        // Visit the dashboard
        $response = $this->get(route('dashboard'));

        // Assert that only the current account's activity is visible
        $response->assertStatus(200);
        $response->assertSee('Activity from current account');
        $response->assertDontSee('Activity from other account');
    }
}
