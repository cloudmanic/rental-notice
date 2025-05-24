<?php

namespace App\Livewire\Notices;

use App\Models\Agent;
use App\Models\Notice;
use App\Models\NoticeType;
use App\Models\Tenant;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Edit extends Component
{
    public $notice;

    public $selectedTenants = [];

    public $search = '';

    public $searchTenant = '';

    public $visibleCharges = 0;

    // Tenant search results
    public $tenants;

    // Auth check and tenant selection
    #[Layout('layouts.app')]
    public function mount(Notice $notice)
    {
        // Authorization check - can only edit notices in your own account
        if ($notice->account_id !== auth()->user()->account->id) {
            abort(403, 'Unauthorized action.');
        }

        // Can only edit notices in the pending payment status
        if ($notice->status !== Notice::STATUS_PENDING_PAYMENT) {
            return redirect()->route('notices.show', $notice->id)
                ->with('error', 'Only notices in pending payment status can be edited.');
        }

        // Load the notice with its relationships
        $notice->load(['noticeType', 'agent', 'tenants']);

        // Convert notice model to array for Livewire binding
        $this->notice = [
            'id' => $notice->id,
            'notice_type_id' => $notice->notice_type_id,
            'agent_id' => $notice->agent_id,
            'past_due_rent' => $notice->past_due_rent,
            'late_charges' => $notice->late_charges,
            'other_1_title' => $notice->other_1_title ?? '',
            'other_1_price' => $notice->other_1_price ?? 0,
            'other_2_title' => $notice->other_2_title ?? '',
            'other_2_price' => $notice->other_2_price ?? 0,
            'other_3_title' => $notice->other_3_title ?? '',
            'other_3_price' => $notice->other_3_price ?? 0,
            'other_4_title' => $notice->other_4_title ?? '',
            'other_4_price' => $notice->other_4_price ?? 0,
            'other_5_title' => $notice->other_5_title ?? '',
            'other_5_price' => $notice->other_5_price ?? 0,
            'payment_other_means' => $notice->payment_other_means ?? false,
            'include_all_other_occupents' => $notice->include_all_other_occupents ?? false,
        ];

        // Initialize tenants as an empty collection
        $this->tenants = collect();

        // Load existing tenants
        foreach ($notice->tenants as $tenant) {
            $this->selectedTenants[] = [
                'id' => $tenant->id,
                'name' => $tenant->full_name,
            ];
        }

        // Count visible charges
        $this->countVisibleCharges();
    }

    public function render()
    {
        $accountId = Auth::user()->account->id;

        // Filter notice types based on the account's plan date (exact match)
        $accountPlanDate = Auth::user()->account->notice_type_plan_date;
        $noticeTypes = NoticeType::when($accountPlanDate, function ($query) use ($accountPlanDate) {
            return $query->where('plan_date', '=', $accountPlanDate);
        })->get();

        // Ensure the current notice type is included in the list even if it doesn't match the filter
        if (isset($this->notice['notice_type_id']) && $this->notice['notice_type_id'] && ! $noticeTypes->contains('id', $this->notice['notice_type_id'])) {
            $currentNoticeType = NoticeType::find($this->notice['notice_type_id']);
            if ($currentNoticeType) {
                $noticeTypes->push($currentNoticeType);
            }
        }

        $agents = Agent::where('account_id', $accountId)->get();

        // Get tenant search results if needed
        if (strlen($this->searchTenant) >= 2) {
            $this->tenants = Tenant::where('account_id', $accountId)
                ->where(function ($query) {
                    $query->where('first_name', 'like', '%'.$this->searchTenant.'%')
                        ->orWhere('last_name', 'like', '%'.$this->searchTenant.'%')
                        ->orWhere('email', 'like', '%'.$this->searchTenant.'%');
                })
                ->get();
        }

        return view('livewire.notices.edit', [
            'noticeTypes' => $noticeTypes,
            'agents' => $agents,
            'states' => config('states'),
        ]);
    }

    // Count how many other charges are currently visible based on filled data
    public function countVisibleCharges()
    {
        $this->visibleCharges = 0;
        for ($i = 1; $i <= 5; $i++) {
            $titleField = "other_{$i}_title";
            $priceField = "other_{$i}_price";
            if (! empty($this->notice[$titleField]) || $this->notice[$priceField] > 0) {
                $this->visibleCharges = $i;
            }
        }
    }

    // Add another charge field
    public function addCharge()
    {
        if ($this->visibleCharges < 5) {
            $this->visibleCharges++;
        }
    }

    // Remove a charge field
    public function removeCharge($index)
    {
        // Clear the data for this charge
        $titleField = "other_{$index}_title";
        $priceField = "other_{$index}_price";
        $this->notice[$titleField] = '';
        $this->notice[$priceField] = 0;

        // Shift all charges above this one down
        for ($i = $index; $i < 5; $i++) {
            $currentTitleField = "other_{$i}_title";
            $currentPriceField = "other_{$i}_price";
            $nextTitleField = 'other_'.($i + 1).'_title';
            $nextPriceField = 'other_'.($i + 1).'_price';

            if ($i < 5) {
                $this->notice[$currentTitleField] = $this->notice[$nextTitleField] ?? '';
                $this->notice[$currentPriceField] = $this->notice[$nextPriceField] ?? 0;
            }
        }

        // Clear the last charge if we've shifted everything down
        if ($this->visibleCharges == 5) {
            $this->notice['other_5_title'] = '';
            $this->notice['other_5_price'] = 0;
        }

        $this->visibleCharges--;
    }

    // Add a tenant to the selected list
    public function addTenant($id)
    {
        $tenant = Tenant::find($id);
        if (! $tenant) {
            return;
        }

        // Check if this tenant is already selected
        if (! in_array($id, array_column($this->selectedTenants, 'id'))) {
            $this->selectedTenants[] = [
                'id' => $tenant->id,
                'name' => $tenant->full_name,
            ];
        }

        // Clear the search field after selection
        $this->searchTenant = '';
    }

    // Remove a tenant from the selected list
    public function removeTenant($id)
    {
        $this->selectedTenants = array_filter($this->selectedTenants, function ($tenant) use ($id) {
            return $tenant['id'] != $id;
        });

        // Re-index the array
        $this->selectedTenants = array_values($this->selectedTenants);
    }

    // Update the notice
    public function updateNotice()
    {
        $user = Auth::user();
        $accountId = $user->account->id;
        $accountPlanDate = $user->account->notice_type_plan_date;

        // Get allowed notice type IDs based on plan date (exact match)
        $allowedNoticeTypeIds = NoticeType::when($accountPlanDate, function ($query) use ($accountPlanDate) {
            return $query->where('plan_date', '=', $accountPlanDate);
        })->pluck('id')->toArray();

        $validatedData = $this->validate([
            // 'notice.notice_type_id' => [
            //     'required',
            //     'exists:notice_types,id',
            //     function ($attribute, $value, $fail) use ($allowedNoticeTypeIds) {
            //         if (!in_array($value, $allowedNoticeTypeIds)) {
            //             $fail('The selected notice type is not available for your current plan.');
            //         }
            //     },
            // ],
            'notice.agent_id' => 'required|exists:agents,id',
            'selectedTenants' => 'required|array|min:1',
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
        ], [
            'selectedTenants.required' => 'Please select at least one tenant.',
            'selectedTenants.min' => 'Please select at least one tenant.',
        ]);

        // Get the notice model from database
        $noticeModel = Notice::find($this->notice['id']);

        // Get the notice type for pricing
        $noticeType = NoticeType::find($this->notice['notice_type_id']);

        // Update the notice model with form data
        $noticeModel->fill([
            'notice_type_id' => $this->notice['notice_type_id'],
            'agent_id' => $this->notice['agent_id'],
            'past_due_rent' => $this->notice['past_due_rent'],
            'late_charges' => $this->notice['late_charges'],
            'other_1_title' => $this->notice['other_1_title'],
            'other_1_price' => $this->notice['other_1_price'],
            'other_2_title' => $this->notice['other_2_title'],
            'other_2_price' => $this->notice['other_2_price'],
            'other_3_title' => $this->notice['other_3_title'],
            'other_3_price' => $this->notice['other_3_price'],
            'other_4_title' => $this->notice['other_4_title'],
            'other_4_price' => $this->notice['other_4_price'],
            'other_5_title' => $this->notice['other_5_title'],
            'other_5_price' => $this->notice['other_5_price'],
            'payment_other_means' => $this->notice['payment_other_means'],
            'include_all_other_occupents' => $this->notice['include_all_other_occupents'],
            'price' => $noticeType->price,
        ]);

        // Save the notice
        $noticeModel->save();

        // Sync tenants
        $tenantIds = array_column($this->selectedTenants, 'id');
        $noticeModel->tenants()->sync($tenantIds);

        // Redirect to the notice details page
        return redirect()->route('notices.preview', $this->notice['id'])
            ->with('success', 'Notice updated successfully.');
    }
}
