<?php

namespace App\Services;

use App\Models\Activity;
use Illuminate\Support\Facades\Auth;

class ActivityService
{
    /**
     * Log an activity
     *
     * @param string $description The description of the activity
     * @param int|null $tenantId The tenant ID if related to a tenant
     * @param int|null $noticeId The notice ID if related to a notice
     * @param int|null $agentId The agent ID if related to an agent
     * @return Activity
     */
    public static function log(
        string $description,
        ?int $tenantId = null,
        ?int $noticeId = null,
        ?int $agentId = null
    ): Activity {
        $userId = Auth::id();
        $accountId = Auth::user()->currentAccount->id;

        return Activity::create([
            'account_id' => $accountId,
            'user_id' => $userId,
            'tenant_id' => $tenantId,
            'notice_id' => $noticeId,
            'agent_id' => $agentId,
            'description' => $description,
        ]);
    }
}
