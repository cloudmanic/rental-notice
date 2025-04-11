<?php

namespace App\Livewire\Notices;

use App\Models\Notice;
use Livewire\Component;
use Livewire\Attributes\Layout;

class Preview extends Component
{
    public Notice $notice;

    #[Layout('layouts.app')]
    public function mount(Notice $notice)
    {
        $this->notice = $notice->load(['noticeType', 'tenants', 'agent', 'user']);

        // Authorization check - can only view notices in your own account
        if ($this->notice->account_id !== auth()->user()->account->id) {
            abort(403, 'Unauthorized action.');
        }

        // // Can only preview draft notices
        // if ($this->notice->status !== Notice::STATUS_DRAFT) {
        //     return redirect()->route('notices.show', $notice->id)
        //         ->with('error', 'This notice is no longer in draft status.');
        // }
    }

    public function proceedToPayment()
    {
        // Update the notice status to pending payment
        $this->notice->status = Notice::STATUS_PENDING_PAYMENT;
        $this->notice->save();

        // Redirect to payment page (you'll need to create this later)
        // For now, we'll redirect to the notice show page
        return redirect()->route('notices.show', $this->notice->id)
            ->with('success', 'Notice status updated to pending payment.');
    }

    public function backToEdit()
    {
        return redirect()->route('notices.edit', $this->notice->id);
    }

    public function render()
    {
        return view('livewire.notices.preview');
    }
}