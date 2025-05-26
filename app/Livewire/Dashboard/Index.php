<?php

namespace App\Livewire\Dashboard;

use App\Models\Activity;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Dashboard - Oregon Past Due Rent')]
class Index extends Component
{
    use WithPagination;

    /**
     * Determine the type of activity based on event column or which ID is populated.
     */
    protected function determineActivityType(Activity $activity): string
    {
        // If the event column is already set, use it
        if ($activity->event) {
            return $activity->event;
        }

        // Fall back to the old logic for backward compatibility
        if ($activity->tenant_id) {
            return 'Tenant';
        } elseif ($activity->notice_id) {
            return 'Notice';
        } elseif ($activity->agent_id) {
            return 'Agent';
        } elseif ($activity->user_id === null) {
            return 'System';
        } else {
            return 'Account';  // Default when only account_id and user_id are present
        }
    }

    #[Layout('layouts.app')]
    public function render()
    {
        $activities = Activity::with(['user', 'tenant', 'notice', 'agent'])
            ->where('account_id', Auth::user()->account->id)
            ->latest()
            ->paginate(50);

        // Add activity type to each activity
        foreach ($activities as $activity) {
            $activity->type = $this->determineActivityType($activity);
        }

        return view('livewire.dashboard.index', [
            'activities' => $activities,
        ]);
    }
}
