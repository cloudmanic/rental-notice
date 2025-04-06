<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\Agent;
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
     * @param string|null $event The event type (Agent, Tenant, Notice, System, Error)
     * @return Activity
     */
    public static function log(
        string $description,
        ?int $tenantId = null,
        ?int $noticeId = null,
        ?int $agentId = null,
        ?string $event = null
    ): Activity {
        $userId = Auth::id();
        $user = Auth::user();
        
        // Get account_id from currentAccount if available, otherwise from relations
        $accountId = null;
        
        if ($user && property_exists($user, 'currentAccount') && $user->currentAccount) {
            // Use currentAccount if it's set
            $accountId = $user->currentAccount->id;
        } elseif ($user && $user->account) {
            // Use account relation if it exists
            $accountId = $user->account->id;
        } elseif ($agentId) {
            // If we have an agent ID, we can get the account from there
            $accountId = Agent::find($agentId)->account_id;
        } elseif ($tenantId || $noticeId) {
            // For tenant or notice related activities, we could get account from there
            // This would require adding those model imports and relationships
            // Implement as needed
        }
        
        // If still null (unlikely in production), fallback to first account
        if (!$accountId && $user) {
            $accountId = $user->accounts()->first()->id ?? 1;
        }

        // Determine event type if not provided
        if (!$event) {
            if ($agentId) {
                $event = 'Agent';
            } elseif ($tenantId) {
                $event = 'Tenant';
            } elseif ($noticeId) {
                $event = 'Notice';
            } elseif (!$userId) {
                $event = 'System';
            } else {
                $event = 'Account';
            }
        }

        return Activity::create([
            'account_id' => $accountId,
            'user_id' => $userId,
            'tenant_id' => $tenantId,
            'notice_id' => $noticeId,
            'agent_id' => $agentId,
            'description' => $description,
            'event' => $event,
        ]);
    }
}
