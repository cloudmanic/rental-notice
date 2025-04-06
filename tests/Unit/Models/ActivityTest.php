<?php

namespace Tests\Unit\Models;

use App\Models\Activity;
use App\Models\Account;
use App\Models\Agent;
use App\Models\Notice;
use App\Models\NoticeType;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ActivityTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_has_correct_fillable_attributes()
    {
        $activity = new Activity();
        $this->assertEquals([
            'account_id',
            'user_id',
            'tenant_id',
            'notice_id',
            'agent_id',
            'description',
        ], $activity->getFillable());
    }

    #[Test]
    public function it_belongs_to_an_account()
    {
        $activity = Activity::factory()->create();
        $this->assertInstanceOf(Account::class, $activity->account);
    }

    #[Test]
    public function it_belongs_to_a_user()
    {
        $activity = Activity::factory()->create();
        $this->assertInstanceOf(User::class, $activity->user);
    }

    #[Test]
    public function it_can_belong_to_a_tenant()
    {
        $tenant = Tenant::factory()->create();
        $activity = Activity::factory()->create(['tenant_id' => $tenant->id]);

        $this->assertInstanceOf(Tenant::class, $activity->tenant);
    }

    #[Test]
    public function it_can_belong_to_a_notice()
    {
        // Create dependencies first
        $account = Account::factory()->create();
        $user = User::factory()->create();
        $noticeType = NoticeType::factory()->create();

        // Create a notice with explicit values for required fields
        $notice = Notice::factory()->create([
            'account_id' => $account->id,
            'user_id' => $user->id,
            'notice_type_id' => $noticeType->id
        ]);

        // Now create the activity linked to this notice
        $activity = Activity::factory()->create([
            'account_id' => $account->id,
            'notice_id' => $notice->id
        ]);

        $this->assertInstanceOf(Notice::class, $activity->notice);
    }

    #[Test]
    public function it_can_belong_to_an_agent()
    {
        $agent = Agent::factory()->create();
        $activity = Activity::factory()->create(['agent_id' => $agent->id]);

        $this->assertInstanceOf(Agent::class, $activity->agent);
    }
}
