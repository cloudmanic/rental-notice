<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\Activity;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    use WithPagination;

    /**
     * Determine the type of activity based on which ID is populated.
     *
     * @param Activity $activity
     * @return string
     */
    protected function determineActivityType(Activity $activity): string
    {
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
