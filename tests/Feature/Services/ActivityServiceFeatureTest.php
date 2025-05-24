<?php

namespace Tests\Feature\Services;

use App\Livewire\Notices\Create;
use App\Livewire\Tenants\Create as TenantCreate;
use App\Livewire\Tenants\Edit as TenantEdit;
use App\Models\Account;
use App\Models\Activity;
use App\Models\Agent;
use App\Models\Notice;
use App\Models\NoticeType;
use App\Models\Tenant;
use App\Models\User;
use App\Services\ActivityService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
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
            'last_name' => 'Doe',
        ]);

        // Log the tenant creation activity
        $description = "Created tenant: {$tenant->full_name}";
        ActivityService::log($description, $tenant->id);

        // Check that the activity was logged correctly
        $this->assertDatabaseHas('activities', [
            'account_id' => $this->account->id,
            'user_id' => $this->user->id,
            'tenant_id' => $tenant->id,
            'description' => $description,
        ]);

        // Check that the activity appears on the dashboard
        $response = $this->get(route('dashboard'));
        $response->assertSee($description);
        $response->assertSee('Tenant');
    }

    #[Test]
    public function it_logs_tenant_creation_with_placeholder()
    {
        // Create a tenant
        $tenant = Tenant::factory()->create([
            'account_id' => $this->account->id,
            'first_name' => 'Jane',
            'last_name' => 'Smith',
        ]);

        // Log the tenant creation activity with {name} placeholder
        $description = '{name} was added as a new tenant.';
        ActivityService::log($description, $tenant->id);

        // Check that the activity was logged correctly with the placeholder
        $this->assertDatabaseHas('activities', [
            'account_id' => $this->account->id,
            'user_id' => $this->user->id,
            'tenant_id' => $tenant->id,
            'description' => $description,
        ]);

        // Check that the activity appears on the dashboard with the placeholder replaced
        $response = $this->get(route('dashboard'));
        $this->assertStringNotContainsString('{name}', $response->getContent());
        $this->assertStringContainsString('Jane Smith was added as a new tenant.', $response->getContent());
    }

    #[Test]
    public function it_logs_notice_creation_activity()
    {
        // Create a notice with proper dependencies
        $notice = Notice::factory()->create([
            'account_id' => $this->account->id,
            'user_id' => $this->user->id,
            'notice_type_id' => $this->noticeType->id,
        ]);

        // Log the notice creation activity
        $description = "Created notice #{$notice->id}";
        ActivityService::log($description, null, $notice->id);

        // Check that the activity was logged correctly
        $this->assertDatabaseHas('activities', [
            'account_id' => $this->account->id,
            'user_id' => $this->user->id,
            'notice_id' => $notice->id,
            'description' => $description,
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
            'name' => 'Agent Smith',
        ]);

        // Log the agent creation activity
        $description = "Created agent: {$agent->name}";
        ActivityService::log($description, null, null, $agent->id);

        // Check that the activity was logged correctly
        $this->assertDatabaseHas('activities', [
            'account_id' => $this->account->id,
            'user_id' => $this->user->id,
            'agent_id' => $agent->id,
            'description' => $description,
        ]);

        // Check that the activity appears on the dashboard
        $response = $this->get(route('dashboard'));
        $response->assertSee($description);
        $response->assertSee('Agent');
    }

    #[Test]
    public function it_logs_agent_creation_with_placeholder()
    {
        // Create an agent
        $agent = Agent::factory()->create([
            'account_id' => $this->account->id,
            'name' => 'Agent Smith',
        ]);

        // Log the agent creation activity with {name} placeholder
        $description = '{name} was added as a new agent.';
        ActivityService::log($description, null, null, $agent->id);

        // Check that the activity was logged correctly with the placeholder
        $this->assertDatabaseHas('activities', [
            'account_id' => $this->account->id,
            'user_id' => $this->user->id,
            'agent_id' => $agent->id,
            'description' => $description,
        ]);

        // Check that the activity appears on the dashboard with the placeholder replaced
        $response = $this->get(route('dashboard'));
        $this->assertStringNotContainsString('{name}', $response->getContent());
        $this->assertStringContainsString('Agent Smith was added as a new agent.', $response->getContent());
    }

    #[Test]
    public function it_logs_agent_creation_during_notice_creation()
    {
        // Test the agent creation functionality within the notice creation process
        $agentData = [
            'name' => 'New Test Agent',
            'email' => 'testnoticeagent@example.com',
            'phone' => '555-123-4567',
            'address_1' => '123 Main St',
            'address_2' => 'Apt 4B',
            'city' => 'Portland',
            'state' => 'OR',
            'zip' => '97205',
        ];

        // Test the Livewire component's agent creation method
        Livewire::test(Create::class)
            ->set('agent.name', $agentData['name'])
            ->set('agent.email', $agentData['email'])
            ->set('agent.phone', $agentData['phone'])
            ->set('agent.address_1', $agentData['address_1'])
            ->set('agent.address_2', $agentData['address_2'])
            ->set('agent.city', $agentData['city'])
            ->set('agent.state', $agentData['state'])
            ->set('agent.zip', $agentData['zip'])
            ->call('createAgent');

        // Check that the agent was created
        $agent = Agent::where('name', $agentData['name'])->first();
        $this->assertNotNull($agent);

        // Check that the activity log was created with the expected format
        $this->assertDatabaseHas('activities', [
            'account_id' => $this->account->id,
            'user_id' => $this->user->id,
            'agent_id' => $agent->id,
            'description' => '{name} was added as a new agent.',
            'event' => 'Agent',
        ]);
    }

    #[Test]
    public function it_logs_tenant_creation_during_notice_creation()
    {
        // Test the tenant creation functionality within the notice creation process
        $tenantData = [
            'first_name' => 'John',
            'last_name' => 'Notice',
            'email' => 'johnnotice@example.com',
            'phone' => '555-123-4567',
            'address_1' => '456 Tenant St',
            'address_2' => 'Unit 7C',
            'city' => 'Portland',
            'state' => 'OR',
            'zip' => '97205',
        ];

        // Test the Livewire component's tenant creation method
        Livewire::test(Create::class)
            ->set('tenant.first_name', $tenantData['first_name'])
            ->set('tenant.last_name', $tenantData['last_name'])
            ->set('tenant.email', $tenantData['email'])
            ->set('tenant.phone', $tenantData['phone'])
            ->set('tenant.address_1', $tenantData['address_1'])
            ->set('tenant.address_2', $tenantData['address_2'])
            ->set('tenant.city', $tenantData['city'])
            ->set('tenant.state', $tenantData['state'])
            ->set('tenant.zip', $tenantData['zip'])
            ->call('createTenant');

        // Check that the tenant was created
        $tenant = Tenant::where('email', $tenantData['email'])->first();
        $this->assertNotNull($tenant);

        // Check that the activity log was created with the expected format
        $this->assertDatabaseHas('activities', [
            'account_id' => $this->account->id,
            'user_id' => $this->user->id,
            'tenant_id' => $tenant->id,
            'description' => '{name} was added as a new tenant.',
            'event' => 'Tenant',
        ]);
    }

    #[Test]
    public function it_logs_tenant_creation_from_tenant_screen()
    {
        $tenantData = [
            'first_name' => 'Sarah',
            'last_name' => 'Johnson',
            'email' => 'sarah@example.com',
            'phone' => '555-987-6543',
            'address_1' => '789 Oak St',
            'address_2' => '',
            'city' => 'Portland',
            'state' => 'OR',
            'zip' => '97206',
        ];

        // Test the Tenants Create component
        Livewire::test(TenantCreate::class)
            ->set('first_name', $tenantData['first_name'])
            ->set('last_name', $tenantData['last_name'])
            ->set('email', $tenantData['email'])
            ->set('phone', $tenantData['phone'])
            ->set('address_1', $tenantData['address_1'])
            ->set('address_2', $tenantData['address_2'])
            ->set('city', $tenantData['city'])
            ->set('state', $tenantData['state'])
            ->set('zip', $tenantData['zip'])
            ->call('save');

        // Check that the tenant was created
        $tenant = Tenant::where('email', $tenantData['email'])->first();
        $this->assertNotNull($tenant);

        // Check that the activity log was created
        $this->assertDatabaseHas('activities', [
            'account_id' => $this->account->id,
            'user_id' => $this->user->id,
            'tenant_id' => $tenant->id,
            'description' => '{name} was added as a new tenant.',
            'event' => 'Tenant',
        ]);
    }

    #[Test]
    public function it_logs_tenant_update_from_edit_screen()
    {
        // Create a tenant first
        $tenant = Tenant::factory()->create([
            'account_id' => $this->account->id,
            'first_name' => 'Original',
            'last_name' => 'Name',
            'email' => 'original@example.com',
        ]);

        // Test updating the tenant
        Livewire::test(TenantEdit::class, ['tenant' => $tenant])
            ->set('first_name', 'Updated')
            ->set('last_name', 'Tenant')
            ->set('email', 'updated@example.com')
            ->call('update');

        // Check that the tenant was updated
        $updatedTenant = Tenant::find($tenant->id);
        $this->assertEquals('Updated', $updatedTenant->first_name);
        $this->assertEquals('Tenant', $updatedTenant->last_name);

        // Check that the activity log was created
        $this->assertDatabaseHas('activities', [
            'account_id' => $this->account->id,
            'user_id' => $this->user->id,
            'tenant_id' => $tenant->id,
            'description' => '{name}\'s information was updated.',
            'event' => 'Tenant',
        ]);
    }

    #[Test]
    public function it_logs_tenant_deletion()
    {
        // Create a tenant first
        $tenant = Tenant::factory()->create([
            'account_id' => $this->account->id,
            'first_name' => 'Delete',
            'last_name' => 'Me',
            'email' => 'delete@example.com',
        ]);

        $tenantId = $tenant->id;
        $tenantName = $tenant->full_name;

        // Test deleting the tenant
        Livewire::test(TenantEdit::class, ['tenant' => $tenant])
            ->call('confirmDelete')
            ->assertSet('showDeleteModal', true)
            ->call('delete');

        // Check that the tenant was deleted
        $this->assertDatabaseMissing('tenants', [
            'id' => $tenantId,
        ]);

        // Check that the activity log was created
        $this->assertDatabaseHas('activities', [
            'account_id' => $this->account->id,
            'user_id' => $this->user->id,
            'tenant_id' => null, // Tenant ID should be null since tenant is deleted
            'description' => "Tenant {$tenantName} was deleted.",
            'event' => 'Tenant',
        ]);
    }

    #[Test]
    public function it_logs_multiple_activities_and_displays_them_in_order()
    {
        // Create entities
        $tenant = Tenant::factory()->create(['account_id' => $this->account->id]);
        $notice = Notice::factory()->create([
            'account_id' => $this->account->id,
            'user_id' => $this->user->id,
            'notice_type_id' => $this->noticeType->id,
        ]);
        $agent = Agent::factory()->create(['account_id' => $this->account->id]);

        // Log activities in a specific order with slight delays to ensure correct order
        $description1 = 'First activity';
        ActivityService::log($description1, $tenant->id);
        sleep(1); // Wait 1 second

        $description2 = 'Second activity';
        ActivityService::log($description2, null, $notice->id);
        sleep(1); // Wait 1 second

        $description3 = 'Third activity';
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
