<?php

namespace App\Livewire\Notices;

use App\Models\Notice;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

class Generating extends Component
{
    public $notice;

    // Poll every 3 seconds
    public $pollingInterval = 3000;

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.notices.generating');
    }

    /**
     * Mount the component with the notice instance.
     *
     * @param Notice $notice
     * @return void
     */
    public function mount(Notice $notice)
    {
        $this->notice = $notice;

        if (!$this->notice) {
            session()->flash('error', 'Notice not found or access denied.');
            return redirect()->route('notices.index');
        }
    }

    /**
     * Check the status of the PDF generation.
     *
     * @return void
     */
    public function checkPdfStatus()
    {
        // Check if the draft PDF has been generated
        if ($this->notice && !empty($this->notice->draft_pdf)) {
            // PDF is ready, redirect to the preview page
            return redirect()->route('notices.preview', $this->notice->id);
        }

        return null;
    }
}
