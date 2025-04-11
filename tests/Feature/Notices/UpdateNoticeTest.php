<?php

namespace Tests\Feature\Notices;

use Tests\TestCase;
use App\Models\Notice;
use App\Models\User;
use App\Models\Account;
use App\Models\Agent;
use App\Models\NoticeType;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateNoticeTest extends TestCase
{
    use RefreshDatabase;

    public function test_notice_can_be_marked_as_served()
    {
        // Create dependencies for the Notice factory
        $account = Account::factory()->create();
        $user = User::factory()->create();

        // Attach user to account using the many-to-many relationship
        $user->accounts()->attach($account->id, ['is_owner' => true]);

        $agent = Agent::factory()->create(['account_id' => $account->id]);
        $noticeType = NoticeType::factory()->create();

        // Create a notice with service_pending status
        $notice = Notice::factory()->create([
            'account_id' => $account->id,
            'user_id' => $user->id,
            'agent_id' => $agent->id,
            'notice_type_id' => $noticeType->id,
            'status' => Notice::STATUS_SERVICE_PENDING
        ]);

        // Test logic to mark as served
        $notice->status = Notice::STATUS_SERVED;
        $notice->save();

        $this->assertEquals(Notice::STATUS_SERVED, $notice->fresh()->status);
    }
}
