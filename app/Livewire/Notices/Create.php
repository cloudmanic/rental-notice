<?php

namespace App\Livewire\Notices;

use App\Models\Agent;
use App\Models\Notice;
use App\Models\Tenant;
use App\Models\NoticeType;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

class Create extends Component
{
    public $notice = [
        'notice_type_id' => '',
        'agent_id' => '',
        'tenant_id' => '',
        'past_due_rent' => 0,
        'late_charges' => 0,
        'other_1_title' => '',
        'other_1_price' => 0,
        'other_2_title' => '',
        'other_2_price' => 0,
        'other_3_title' => '',
        'other_3_price' => 0,
        'other_4_title' => '',
        'other_4_price' => 0,
        'other_5_title' => '',
        'other_5_price' => 0,
        'payment_other_means' => false,
        'include_all_other_occupents' => false,
    ];

    public $showTenantModal = false;
    public $showAgentModal = false;
    public $searchTenant = '';
    public $selectedTenantId = null;

    #[Layout('layouts.app')]
    public function render()
    {
        $user = Auth::user();
        $accountId = Auth::user()->account->id;

        // Get the account's notice type plan date
        $accountPlanDate = $user->account->notice_type_plan_date;

        // Filter notice types based on the account's plan date
        $noticeTypes = NoticeType::when($accountPlanDate, function ($query) use ($accountPlanDate) {
            return $query->where('plan_date', '<=', $accountPlanDate);
        })->get();

        $agents = Agent::where('account_id', $accountId)->get();

        $tenants = collect();
        if (strlen($this->searchTenant) >= 2) {
            $tenants = Tenant::where('account_id', $accountId)
                ->where(function ($query) {
                    $query->where('first_name', 'like', '%' . $this->searchTenant . '%')
                        ->orWhere('last_name', 'like', '%' . $this->searchTenant . '%')
                        ->orWhere('email', 'like', '%' . $this->searchTenant . '%');
                })
                ->get();
        }

        return view('livewire.notices.create', [
            'noticeTypes' => $noticeTypes,
            'agents' => $agents,
            'tenants' => $tenants,
        ]);
    }

    public function openTenantModal()
    {
        $this->showTenantModal = true;
    }

    public function closeTenantModal()
    {
        $this->showTenantModal = false;
    }

    public function openAgentModal()
    {
        $this->showAgentModal = true;
    }

    public function closeAgentModal()
    {
        $this->showAgentModal = false;
    }

    public function selectTenant($id)
    {
        $this->notice['tenant_id'] = $id;
        $this->searchTenant = Tenant::find($id)->full_name;
        $this->selectedTenantId = $id;
    }

    public function createNotice()
    {
        $user = Auth::user();
        $accountPlanDate = $user->account->notice_type_plan_date;

        // Get allowed notice type IDs based on plan date
        $allowedNoticeTypeIds = NoticeType::when($accountPlanDate, function ($query) use ($accountPlanDate) {
            return $query->where('plan_date', '<=', $accountPlanDate);
        })->pluck('id')->toArray();

        $validatedData = $this->validate([
            'notice.notice_type_id' => [
                'required',
                'exists:notice_types,id',
                function ($attribute, $value, $fail) use ($allowedNoticeTypeIds) {
                    if (!in_array($value, $allowedNoticeTypeIds)) {
                        $fail('The selected notice type is not available for your current plan.');
                    }
                },
            ],
            'notice.agent_id' => 'required|exists:agents,id',
            'notice.tenant_id' => 'required|exists:tenants,id',
            'notice.past_due_rent' => 'required|numeric|min:0|max:99999.99',
            'notice.late_charges' => 'required|numeric|min:0|max:99999.99',
            'notice.other_1_title' => 'nullable|string|max:255',
            'notice.other_1_price' => 'nullable|numeric|min:0|max:99999.99',
            'notice.other_2_title' => 'nullable|string|max:255',
            'notice.other_2_price' => 'nullable|numeric|min:0|max:99999.99',
            'notice.other_3_title' => 'nullable|string|max:255',
            'notice.other_3_price' => 'nullable|numeric|min:0|max:99999.99',
            'notice.other_4_title' => 'nullable|string|max:255',
            'notice.other_4_price' => 'nullable|numeric|min:0|max:99999.99',
            'notice.other_5_title' => 'nullable|string|max:255',
            'notice.other_5_price' => 'nullable|numeric|min:0|max:99999.99',
            'notice.payment_other_means' => 'boolean',
            'notice.include_all_other_occupents' => 'boolean',
        ]);

        // Get the notice type for pricing
        $noticeType = NoticeType::find($validatedData['notice']['notice_type_id']);

        // Create the notice
        $notice = new Notice();
        $notice->account_id = $user->account_id;
        $notice->user_id = $user->id;
        $notice->notice_type_id = $validatedData['notice']['notice_type_id'];
        $notice->agent_id = $validatedData['notice']['agent_id'];
        $notice->tenant_id = $validatedData['notice']['tenant_id'];
        $notice->price = $noticeType->price;
        $notice->past_due_rent = $validatedData['notice']['past_due_rent'];
        $notice->late_charges = $validatedData['notice']['late_charges'];
        $notice->other_1_title = $validatedData['notice']['other_1_title'];
        $notice->other_1_price = $validatedData['notice']['other_1_price'];
        $notice->other_2_title = $validatedData['notice']['other_2_title'];
        $notice->other_2_price = $validatedData['notice']['other_2_price'];
        $notice->other_3_title = $validatedData['notice']['other_3_title'];
        $notice->other_3_price = $validatedData['notice']['other_3_price'];
        $notice->other_4_title = $validatedData['notice']['other_4_title'];
        $notice->other_4_price = $validatedData['notice']['other_4_price'];
        $notice->other_5_title = $validatedData['notice']['other_5_title'];
        $notice->other_5_price = $validatedData['notice']['other_5_price'];
        $notice->payment_other_means = $validatedData['notice']['payment_other_means'];
        $notice->include_all_other_occupents = $validatedData['notice']['include_all_other_occupents'];
        $notice->status = Notice::STATUS_DRAFT;
        $notice->save();

        return redirect()->route('notices.index');
    }
}
