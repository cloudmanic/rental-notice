<?php

namespace App\Livewire\Notices;

use App\Models\Notice;
use App\Notifications\NoticeServed;
use App\Services\ActivityService;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Title('Notice Details - Oregon Past Due Rent')]
class Show extends Component
{
    use WithFileUploads;

    public Notice $notice;

    public $certificatePdf;

    public $uploadSuccess = false;

    /**
     * Livewire hook that runs before any update
     */
    public function updating($property, $value)
    {
        // Check authorization when certificatePdf is being uploaded
        if ($property === 'certificatePdf') {
            if (! auth()->user()->isSuperAdmin() && ! session('impersonating')) {
                throw new \Exception('Unauthorized action.');
            }
        }
    }

    /**
     * Mount the component with the given notice.
     *
     * @return void
     */
    #[Layout('layouts.app')]
    public function mount(Notice $notice)
    {
        $this->notice = $notice->load(['noticeType', 'tenants', 'agent', 'user']);

        // Authorization check - can only view notices in your own account
        if ($this->notice->account_id !== auth()->user()->account->id && ! auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized action.');
        }
    }

    /**
     * Upload certificate PDF to S3 and update the notice
     */
    public function uploadCertificatePdf()
    {
        // Validate only super admins can upload
        if (! auth()->user()->isSuperAdmin() && ! session('impersonating')) {
            abort(403, 'Unauthorized action.');
        }

        $this->validate([
            'certificatePdf' => 'required|file|mimes:pdf|max:10240', // 10MB max
        ]);

        // Generate a unique filename
        $filename = $this->notice->account_id.'/certificate_'.$this->notice->id.'.pdf';

        // Store file in S3
        $path = $this->certificatePdf->storeAs('', $filename, 's3');

        // Update notice with certificate PDF path
        $this->notice->update([
            'certificate_pdf' => $path,
            'status' => Notice::STATUS_SERVED, // We know once we update the certificate, the status is served
        ]);

        // Log the notice served activity
        $tenantNames = $this->notice->tenants->pluck('full_name')->join(', ');
        ActivityService::log(
            "{$this->notice->noticeType->name} notice to {$tenantNames} has been served.",
            null,
            $this->notice->id,
            null,
            'Notice'
        );

        // Send email notification to the user
        $this->notice->user->notify(new NoticeServed($this->notice));

        // Log the email notification activity
        ActivityService::log(
            "Notice served confirmation email sent to {$this->notice->user->email}.",
            null,
            $this->notice->id,
            null,
            'Notice'
        );

        Log::info('Certificate PDF uploaded for notice ID: '.$this->notice->id);

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
