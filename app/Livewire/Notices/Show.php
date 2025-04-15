<?php

namespace App\Livewire\Notices;

use App\Models\Notice;
use Livewire\Component;
use Livewire\Attributes\Layout;

class Show extends Component
{
    public Notice $notice;

    /**
     * Mount the component with the given notice.
     *
     * @param  \App\Models\Notice  $notice
     * @return void
     */
    #[Layout('layouts.app')]
    public function mount(Notice $notice)
    {
        $this->notice = $notice->load(['noticeType', 'tenants', 'agent', 'user']);

        // Authorization check - can only view notices in your own account
        if ($this->notice->account_id !== auth()->user()->account->id) {
            abort(403, 'Unauthorized action.');
        }
    }

    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.notices.show');
    }
}
