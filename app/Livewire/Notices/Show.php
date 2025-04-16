<?php

namespace App\Livewire\Notices;

use App\Models\Notice;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Storage;

class Show extends Component
{
    use WithFileUploads;

    public Notice $notice;
    public $certificatePdf;
    public $uploadSuccess = false;

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
        if ($this->notice->account_id !== auth()->user()->account->id && !auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized action.');
        }
    }

    /**
     * Upload certificate PDF to S3 and update the notice
     */
    public function uploadCertificatePdf()
    {
        // Validate only super admins can upload
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $this->validate([
            'certificatePdf' => 'required|file|mimes:pdf|max:10240', // 10MB max
        ]);

        // Generate a unique filename
        $filename = $this->notice->account_id . '/certificate_' . $this->notice->id . '.pdf';

        // Store file in S3
        $path = $this->certificatePdf->storeAs('', $filename, 's3');

        // Update notice with certificate PDF path
        $this->notice->update([
            'certificate_pdf' => $path
        ]);

        // Reset file upload and set success message
        $this->certificatePdf = null;
        $this->uploadSuccess = true;
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
