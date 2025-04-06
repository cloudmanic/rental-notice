<?php

namespace Tests\Unit\Livewire\Dashboard;

use App\Livewire\Dashboard\Index;
use App\Models\Activity;
use App\Models\Account;
use App\Models\Agent;
use App\Models\Notice;
use App\Models\NoticeType;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use ReflectionMethod;
use Tests\TestCase;

class IndexTest extends TestCase
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

        // Login the user
        $this->actingAs($this->user);
    }

    #[Test]
    public function it_determines_tenant_activity_type()
    {
        $activity = Activity::factory()->create([
            'account_id' => $this->account->id,
            'user_id' => $this->user->id,
            'tenant_id' => Tenant::factory()->create(['account_id' => $this->account->id])->id,
            'notice_id' => null,
            'agent_id' => null,
        ]);

        $type = $this->callDetermineActivityType($activity);

        $this->assertEquals('Tenant', $type);
    }

    #[Test]
    public function it_determines_notice_activity_type()
    {
        // Create a notice with the proper dependencies
        $notice = Notice::factory()->create([
            'account_id' => $this->account->id,
            'user_id' => $this->user->id,
            'notice_type_id' => $this->noticeType->id
        ]);

        // Create an activity for this notice
        $activity = Activity::factory()->create([
            'account_id' => $this->account->id,
            'user_id' => $this->user->id,
            'tenant_id' => null,
            'notice_id' => $notice->id,
            'agent_id' => null,
        ]);

        $type = $this->callDetermineActivityType($activity);

        $this->assertEquals('Notice', $type);
    }

    #[Test]
    public function it_determines_agent_activity_type()
    {
        $activity = Activity::factory()->create([
            'account_id' => $this->account->id,
            'user_id' => $this->user->id,
            'tenant_id' => null,
            'notice_id' => null,
            'agent_id' => Agent::factory()->create(['account_id' => $this->account->id])->id,
        ]);

        $type = $this->callDetermineActivityType($activity);

        $this->assertEquals('Agent', $type);
    }

    #[Test]
    public function it_determines_system_activity_type()
    {
        $activity = Activity::factory()->create([
            'account_id' => $this->account->id,
            'user_id' => null,
            'tenant_id' => null,
            'notice_id' => null,
            'agent_id' => null,
        ]);

        $type = $this->callDetermineActivityType($activity);

        $this->assertEquals('System', $type);
    }

    #[Test]
    public function it_determines_account_activity_type()
    {
        $activity = Activity::factory()->create([
            'account_id' => $this->account->id,
            'user_id' => $this->user->id,
            'tenant_id' => null,
            'notice_id' => null,
            'agent_id' => null,
        ]);

        $type = $this->callDetermineActivityType($activity);

        $this->assertEquals('Account', $type);
    }

    /**
     * Helper to call the protected determineActivityType method
     */
    private function callDetermineActivityType(Activity $activity)
    {
        $dashboardComponent = new Index();

        $reflection = new ReflectionMethod($dashboardComponent, 'determineActivityType');
        $reflection->setAccessible(true);

        return $reflection->invoke($dashboardComponent, $activity);
    }
}
