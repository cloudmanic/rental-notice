<?php

namespace Tests\Unit\Services;

use App\Models\Account;
use App\Models\Activity;
use App\Models\Agent;
use App\Models\Notice;
use App\Models\NoticeType;
use App\Models\Tenant;
use App\Models\User;
use App\Services\ActivityService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ActivityServiceTest extends TestCase
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
        Auth::login($this->user);
    }

    #[Test]
    public function it_logs_activity_with_description_only()
    {
        $description = 'Test activity';

        $activity = ActivityService::log($description);

        $this->assertInstanceOf(Activity::class, $activity);
        $this->assertEquals($description, $activity->description);
        $this->assertEquals($this->account->id, $activity->account_id);
        $this->assertEquals($this->user->id, $activity->user_id);
        $this->assertNull($activity->tenant_id);
        $this->assertNull($activity->notice_id);
        $this->assertNull($activity->agent_id);
        $this->assertEquals('Account', $activity->event);
    }

    #[Test]
    public function it_logs_tenant_activity()
    {
        $tenant = Tenant::factory()->create(['account_id' => $this->account->id]);
        $description = 'Updated tenant information';

        $activity = ActivityService::log($description, $tenant->id);

        $this->assertEquals($description, $activity->description);
        $this->assertEquals($tenant->id, $activity->tenant_id);
        $this->assertEquals('Tenant', $activity->event);
    }

    #[Test]
    public function it_logs_notice_activity()
    {
        // Create a notice with the proper dependencies
        $notice = Notice::factory()->create([
            'account_id' => $this->account->id,
            'user_id' => $this->user->id,
            'notice_type_id' => $this->noticeType->id
        ]);

        $description = 'Created notice';

        $activity = ActivityService::log($description, null, $notice->id);

        $this->assertEquals($description, $activity->description);
        $this->assertEquals($notice->id, $activity->notice_id);
        $this->assertEquals('Notice', $activity->event);
    }

    #[Test]
    public function it_logs_agent_activity()
    {
        $agent = Agent::factory()->create(['account_id' => $this->account->id]);
        $description = 'Updated agent contact';

        $activity = ActivityService::log($description, null, null, $agent->id);

        $this->assertEquals($description, $activity->description);
        $this->assertEquals($agent->id, $activity->agent_id);
        $this->assertEquals('Agent', $activity->event);
    }
    
    #[Test]
    public function it_logs_activity_with_custom_event()
    {
        $description = 'System notification';
        $activity = ActivityService::log($description, null, null, null, 'System');
        
        $this->assertEquals($description, $activity->description);
        $this->assertEquals('System', $activity->event);
    }
}
