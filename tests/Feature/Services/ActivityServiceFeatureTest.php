<?php

namespace Tests\Feature\Services;

use App\Models\Account;
use App\Models\Activity;
use App\Models\Agent;
use App\Models\Notice;
use App\Models\NoticeType;
use App\Models\Tenant;
use App\Models\User;
use App\Services\ActivityService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ActivityServiceFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected $account;
    protected $user;
    protected $noticeType;

    protected function setUp(): void
    {
        parent::setUp();

        // Create an account and user
        $this->account = Account::factory()->create();
        $this->user = User::factory()->create();
        $this->account->users()->attach($this->user, ['is_owner' => true]);

        // Create a notice type for testing
        $this->noticeType = NoticeType::factory()->create();

        // Set the current account for the user
        $this->user->account = $this->account;
        $this->user->currentAccount = $this->account;

        // Login the user
        $this->actingAs($this->user);
    }

    #[Test]
    public function it_logs_tenant_creation_activity()
    {
        // Create a tenant
        $tenant = Tenant::factory()->create([
            'account_id' => $this->account->id,
            'first_name' => 'John',
            'last_name' => 'Doe'
        ]);

        // Log the tenant creation activity
        $description = "Created tenant: {$tenant->full_name}";
        ActivityService::log($description, $tenant->id);

        // Check that the activity was logged correctly
        $this->assertDatabaseHas('activities', [
            'account_id' => $this->account->id,
            'user_id' => $this->user->id,
            'tenant_id' => $tenant->id,
            'description' => $description
        ]);

        // Check that the activity appears on the dashboard
        $response = $this->get(route('dashboard'));
        $response->assertSee($description);
        $response->assertSee('Tenant');
    }

    #[Test]
    public function it_logs_notice_creation_activity()
    {
        // Create a notice with proper dependencies
        $notice = Notice::factory()->create([
            'account_id' => $this->account->id,
            'user_id' => $this->user->id,
            'notice_type_id' => $this->noticeType->id
        ]);

        // Log the notice creation activity
        $description = "Created notice #{$notice->id}";
        ActivityService::log($description, null, $notice->id);

        // Check that the activity was logged correctly
        $this->assertDatabaseHas('activities', [
            'account_id' => $this->account->id,
            'user_id' => $this->user->id,
            'notice_id' => $notice->id,
            'description' => $description
        ]);

        // Check that the activity appears on the dashboard
        $response = $this->get(route('dashboard'));
        $response->assertSee($description);
        $response->assertSee('Notice');
    }

    #[Test]
    public function it_logs_agent_creation_activity()
    {
        // Create an agent
        $agent = Agent::factory()->create([
            'account_id' => $this->account->id,
            'name' => 'Agent Smith'
        ]);

        // Log the agent creation activity
        $description = "Created agent: {$agent->name}";
        ActivityService::log($description, null, null, $agent->id);

        // Check that the activity was logged correctly
        $this->assertDatabaseHas('activities', [
            'account_id' => $this->account->id,
            'user_id' => $this->user->id,
            'agent_id' => $agent->id,
            'description' => $description
        ]);

        // Check that the activity appears on the dashboard
        $response = $this->get(route('dashboard'));
        $response->assertSee($description);
        $response->assertSee('Agent');
    }

    #[Test]
    public function it_logs_multiple_activities_and_displays_them_in_order()
    {
        // Create entities
        $tenant = Tenant::factory()->create(['account_id' => $this->account->id]);
        $notice = Notice::factory()->create([
            'account_id' => $this->account->id,
            'user_id' => $this->user->id,
            'notice_type_id' => $this->noticeType->id
        ]);
        $agent = Agent::factory()->create(['account_id' => $this->account->id]);

        // Log activities in a specific order with slight delays to ensure correct order
        $description1 = "First activity";
        ActivityService::log($description1, $tenant->id);
        sleep(1); // Wait 1 second

        $description2 = "Second activity";
        ActivityService::log($description2, null, $notice->id);
        sleep(1); // Wait 1 second

        $description3 = "Third activity";
        ActivityService::log($description3, null, null, $agent->id);

        // Check that activities appear on the dashboard in reverse chronological order (newest first)
        $response = $this->get(route('dashboard'));

        // Get the response content as a string
        $content = $response->getContent();

        // Check that the activities appear in the right order
        $pos1 = strpos($content, $description1);
        $pos2 = strpos($content, $description2);
        $pos3 = strpos($content, $description3);

        // Third activity should appear before second, which should appear before first
        $this->assertTrue($pos3 < $pos2);
        $this->assertTrue($pos2 < $pos1);
    }
}
