<?php

namespace App\Livewire\Notices;

use App\Models\Notice;
use Livewire\Component;
use Livewire\Attributes\Layout;

class Preview extends Component
{
    public Notice $notice;

    /**
     * Mount the component with the notice instance
     *
     * @param Notice $notice
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

        // Previous code commented out
        if ($this->notice->status !== Notice::STATUS_PENDING_PAYMENT) {
            return redirect()->route('notices.index')
                ->with('error', 'This notice is no longer in pending payment status.');
        }
    }

    /**
     * Keep the notice in draft status and redirect to the notices index
     */
    public function keepAsDraft()
    {
        // Redirect to notices index with a notice message instead of success
        return redirect()->route('notices.index')
            ->with('notice', 'Notice has been kept as a draft.');
    }

    /**
     * Save the notice as a draft and redirect to the notices index
     */
    public function saveAsDraft()
    {
        // Redirect to the notices list
        return redirect()->route('notices.index')
            ->with('success', 'Notice kept as a draft successfully.');
    }

    /**
     * Redirect to the edit page of the notice
     */
    public function backToEdit()
    {
        return redirect()->route('notices.edit', $this->notice->id);
    }

    /**
     * Redirect to the notices index
     */
    public function render()
    {
        return view('livewire.notices.preview');
    }
}
