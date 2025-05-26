<?php

namespace App\Livewire\Notices;

use App\Jobs\GenerateNoticePdfJob;
use App\Models\Agent;
use App\Models\Notice;
use App\Models\NoticeType;
use App\Models\Tenant;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Create Notice - Oregon Past Due Rent')]
class Create extends Component
{
    public $notice = [
        'notice_type_id' => '',
        'agent_id' => '',
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
    ];

    // Array to store the selected tenants
    public $selectedTenants = [];

    // Track visible other charges
    public $visibleCharges = 0;

    public $showMessage = false;

    public $message = '';

    public $messageType = 'success';

    // Agent form properties
    public $agent = [
        'name' => '',
        'email' => '',
        'phone' => '',
        'address_1' => '',
        'address_2' => '',
        'city' => '',
        'state' => 'OR',
        'zip' => '',
    ];

    // Tenant form properties
    public $tenant = [
        'first_name' => '',
        'last_name' => '',
        'email' => '',
        'phone' => '',
        'address_1' => '',
        'address_2' => '',
        'city' => '',
        'state' => 'OR',
        'zip' => '',
    ];

    public $states;

    public $showTenantModal = false;

    public $showAgentModal = false;

    public $searchTenant = '';

    public $selectedTenantId = null;

    // Warning message property
    public $warningMessage = null;

    /*
     * Render the component view.
     *
     * @return \Illuminate\View\View
     */
    #[Layout('layouts.app')]
    public function render()
    {
        $user = Auth::user();
        $accountId = Auth::user()->account->id;

        // Get the account's notice type plan date
        $accountPlanDate = $user->account->notice_type_plan_date;

        // Filter notice types based on the account's plan date (exact match)
        $noticeTypes = NoticeType::when($accountPlanDate, function ($query) use ($accountPlanDate) {
            return $query->where('plan_date', '=', $accountPlanDate);
        })->get();

        $agents = Agent::where('account_id', $accountId)->get();

        $tenants = collect();
        if (strlen($this->searchTenant) >= 2) {
            $tenants = Tenant::where('account_id', $accountId)
                ->where(function ($query) {
                    $query->where('first_name', 'like', '%'.$this->searchTenant.'%')
                        ->orWhere('last_name', 'like', '%'.$this->searchTenant.'%')
                        ->orWhere('email', 'like', '%'.$this->searchTenant.'%');
                })
                ->get();
        }

        return view('livewire.notices.create', [
            'noticeTypes' => $noticeTypes,
            'agents' => $agents,
            'tenants' => $tenants,
        ]);
    }

    /**
     * Mount the component with initial data.
     *
     * @return void
     */
    public function mount(Request $request)
    {
        $this->states = config('states');

        // If agent_id is provided in the URL, set it in the notice data
        if ($request->has('agent_id')) {
            $this->notice['agent_id'] = $request->agent_id;
        }

        // Check for form data in session from previous agent creation
        if (session()->has('form_data')) {
            $formData = session('form_data');

            // Restore all form fields from session data
            foreach ($formData as $key => $value) {
                if (array_key_exists($key, $this->notice)) {
                    $this->notice[$key] = $value;
                }
            }

            // Check how many other charges were visible previously
            $this->countVisibleCharges();
        }

        // Restore selected tenants if available
        if (session()->has('selected_tenants')) {
            $this->selectedTenants = session('selected_tenants');
        }

        // Check for flash messages from the previous request
        if (session()->has('message')) {
            $this->message = session('message');
            $this->messageType = session('messageType', 'success');
            $this->showMessage = true;
        }

        // Check warning conditions on mount
        $this->updateWarningMessage();
    }

    // Count how many other charges are currently visible based on filled data
    public function countVisibleCharges()
    {
        $this->visibleCharges = 0;
        for ($i = 1; $i <= 5; $i++) {
            if (! empty($this->notice["other_{$i}_title"]) || $this->notice["other_{$i}_price"] > 0) {
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
        $this->notice["other_{$index}_title"] = '';
        $this->notice["other_{$index}_price"] = 0;

        // Shift all charges above this one down
        for ($i = $index; $i < 5; $i++) {
            if ($i < 5) {
                $this->notice["other_{$i}_title"] = $this->notice['other_'.($i + 1).'_title'];
                $this->notice["other_{$i}_price"] = $this->notice['other_'.($i + 1).'_price'];
            }
        }

        // Clear the last charge if we've shifted everything down
        if ($this->visibleCharges == 5) {
            $this->notice['other_5_title'] = '';
            $this->notice['other_5_price'] = 0;
        }

        $this->visibleCharges--;
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

    public function clearTenant()
    {
        $this->notice['tenant_id'] = '';
        $this->searchTenant = '';
        $this->selectedTenantId = null;
    }

    /**
     * Check and update warning message based on current date/time and selected notice type.
     *
     * @return void
     */
    public function updateWarningMessage()
    {
        $this->warningMessage = null;

        // Get current PST date and time
        $now = now()->setTimezone('America/Los_Angeles');
        $currentDay = $now->day;
        $currentHour = $now->hour;

        // Only show warnings if it's before 1pm PST
        if ($currentHour >= 13) {
            return;
        }

        // Warning conditions based on the requirements
        if ($currentDay < 5) {
            // Before the 5th - show warning regardless of notice type selection
            $this->warningMessage = 'If you are serving notice to someone who is late this month, you need to wait until the 5th for a 13-day notice or the 8th for a 10-day notice.';

            return; // Exit early since this warning applies regardless of selection
        }

        // For warnings that depend on notice type selection
        if (empty($this->notice['notice_type_id'])) {
            return;
        }

        $noticeType = NoticeType::find($this->notice['notice_type_id']);
        if (! $noticeType) {
            return;
        }

        $is10DayNotice = str_contains($noticeType->name, '10-Day');

        // Check notice-type-specific warnings
        if ($currentDay >= 5 && $currentDay < 8 && $is10DayNotice) {
            // Between 5th and 8th, and they selected a 10-day notice
            $this->warningMessage = 'If you are serving notice to someone who is late this month, you can only serve a 13-day notice until the 8th of the month.';
        }
    }

    /**
     * Handle when notice type is changed.
     *
     * @return void
     */
    public function updatedNoticeNoticeTypeId()
    {
        $this->updateWarningMessage();
    }

    /**
     * Create a new notice.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createNotice()
    {
        $user = Auth::user();
        $accountId = Auth::user()->account->id;
        $accountPlanDate = $user->account->notice_type_plan_date;

        // Get allowed notice type IDs based on plan date (exact match)
        $allowedNoticeTypeIds = NoticeType::when($accountPlanDate, function ($query) use ($accountPlanDate) {
            return $query->where('plan_date', '=', $accountPlanDate);
        })->pluck('id')->toArray();

        $validatedData = $this->validate([
            'notice.notice_type_id' => [
                'required',
                'exists:notice_types,id',
                function ($attribute, $value, $fail) use ($allowedNoticeTypeIds) {
                    if (! in_array($value, $allowedNoticeTypeIds)) {
                        $fail('The selected notice type is not available for your current plan.');
                    }
                },
            ],
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
        ], [
            'selectedTenants.required' => 'Please select at least one tenant.',
            'selectedTenants.min' => 'Please select at least one tenant.',
        ]);

        // Get the notice type for pricing
        $noticeType = NoticeType::find($validatedData['notice']['notice_type_id']);

        // Create the notice
        $notice = new Notice;
        $notice->account_id = $accountId;
        $notice->user_id = $user->id;
        $notice->notice_type_id = $validatedData['notice']['notice_type_id'];
        $notice->agent_id = $validatedData['notice']['agent_id'];
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
        $notice->status = Notice::STATUS_PENDING_PAYMENT;
        $notice->save();

        // Attach tenants to the notice
        foreach ($this->selectedTenants as $tenant) {
            $notice->tenants()->attach($tenant['id']);
        }

        // Dispatch job to generate the PDF with watermark (draft) before payment
        GenerateNoticePdfJob::dispatch($notice);

        // Redirect to loading page instead of preview
        return redirect()->route('notices.generating', $notice->id);
    }

    /**
     * Create a new agent from the modal form.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createAgent()
    {
        $accountId = Auth::user()->account->id;

        $validatedData = $this->validate([
            'agent.name' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($accountId) {
                    $exists = Agent::where('account_id', $accountId)
                        ->where('name', $value)
                        ->exists();

                    if ($exists) {
                        $fail('An agent with this name already exists.');
                    }
                },
            ],
            'agent.email' => 'nullable|email|max:255',
            'agent.phone' => 'nullable|string|max:12|regex:/^\d{3}-\d{3}-\d{4}$/',
            'agent.address_1' => 'required|string|max:255',
            'agent.address_2' => 'nullable|string|max:255',
            'agent.city' => 'required|string|max:255',
            'agent.state' => 'required|string|size:2',
            'agent.zip' => 'required|string|max:10|regex:/^\d{5}(-\d{4})?$/',
        ], [
            'agent.name.required' => 'The agent name is required.',
            'agent.name.max' => 'The agent name cannot exceed 255 characters.',
            'agent.email.email' => 'Please enter a valid email address.',
            'agent.email.max' => 'The email address cannot exceed 255 characters.',
            'agent.phone.regex' => 'Please enter the phone number in the format XXX-XXX-XXXX.',
            'agent.phone.max' => 'The phone number cannot exceed 12 characters.',
            'agent.address_1.required' => 'The street address is required.',
            'agent.address_1.max' => 'The street address cannot exceed 255 characters.',
            'agent.address_2.max' => 'The additional address information cannot exceed 255 characters.',
            'agent.city.required' => 'The city is required.',
            'agent.city.max' => 'The city name cannot exceed 255 characters.',
            'agent.state.required' => 'The state is required.',
            'agent.state.size' => 'Please select a valid state from the dropdown.',
            'agent.zip.required' => 'The ZIP code is required.',
            'agent.zip.regex' => 'Please enter a valid ZIP code (e.g., 12345 or 12345-6789).',
            'agent.zip.max' => 'The ZIP code cannot exceed 10 characters.',
        ]);

        $agent = new Agent;
        $agent->account_id = $accountId;
        $agent->name = $validatedData['agent']['name'];
        $agent->email = $validatedData['agent']['email'];
        $agent->phone = $validatedData['agent']['phone'];
        $agent->address_1 = $validatedData['agent']['address_1'];
        $agent->address_2 = $validatedData['agent']['address_2'];
        $agent->city = $validatedData['agent']['city'];
        $agent->state = $validatedData['agent']['state'];
        $agent->zip = $validatedData['agent']['zip'];
        $agent->save();

        // Log the agent creation activity during notice creation
        ActivityService::log('{name} was added as a new agent.', null, null, $agent->id, 'Agent');

        // Track all the form data to preserve it after redirect
        $currentFormData = $this->notice;

        // Include the newly created agent ID in the form data
        $currentFormData['agent_id'] = $agent->id;

        // Create the flash message
        session()->flash('message', 'Agent added successfully.');
        session()->flash('messageType', 'success');

        // Store current form state in the session to preserve it across redirects
        session()->flash('form_data', [
            'notice_type_id' => $currentFormData['notice_type_id'] ?? '',
            'agent_id' => $agent->id, // Explicitly set the new agent ID
            'tenant_id' => $currentFormData['tenant_id'] ?? '',
            'past_due_rent' => $currentFormData['past_due_rent'] ?? 0,
            'late_charges' => $currentFormData['late_charges'] ?? 0,
            'other_1_title' => $currentFormData['other_1_title'] ?? '',
            'other_1_price' => $currentFormData['other_1_price'] ?? 0,
            'other_2_title' => $currentFormData['other_2_title'] ?? '',
            'other_2_price' => $currentFormData['other_2_price'] ?? 0,
            'other_3_title' => $currentFormData['other_3_title'] ?? '',
            'other_3_price' => $currentFormData['other_3_price'] ?? 0,
            'other_4_title' => $currentFormData['other_4_title'] ?? '',
            'other_4_price' => $currentFormData['other_4_price'] ?? 0,
            'other_5_title' => $currentFormData['other_5_title'] ?? '',
            'other_5_price' => $currentFormData['other_5_price'] ?? 0,
            'payment_other_means' => isset($currentFormData['payment_other_means']) ? (bool) $currentFormData['payment_other_means'] : false,
        ]);

        // If we have a selected tenant, preserve that information too
        if ($this->selectedTenantId) {
            $tenant = Tenant::find($this->selectedTenantId);
            if ($tenant) {
                session()->flash('tenant_name', $tenant->full_name);
                session()->flash('selected_tenant_id', $tenant->id);
            }
        }

        // Redirect to notices.create - all data comes from session
        return redirect()->route('notices.create');
    }

    /**
     * Create a new tenant from the modal form.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createTenant()
    {
        $accountId = Auth::user()->account->id;

        $validatedData = $this->validate([
            'tenant.first_name' => 'required|string|max:255',
            'tenant.last_name' => 'required|string|max:255',
            'tenant.email' => 'nullable|email|max:255',
            'tenant.phone' => 'nullable|string|max:12|regex:/^\d{3}-\d{3}-\d{4}$/',
            'tenant.address_1' => 'required|string|max:255',
            'tenant.address_2' => 'nullable|string|max:255',
            'tenant.city' => 'required|string|max:255',
            'tenant.state' => 'required|string|size:2',
            'tenant.zip' => 'required|string|max:10|regex:/^\d{5}(-\d{4})?$/',
        ], [
            'tenant.first_name.required' => 'The first name is required.',
            'tenant.first_name.max' => 'The first name cannot exceed 255 characters.',
            'tenant.last_name.required' => 'The last name is required.',
            'tenant.last_name.max' => 'The last name cannot exceed 255 characters.',
            'tenant.email.email' => 'Please enter a valid email address.',
            'tenant.email.max' => 'The email address cannot exceed 255 characters.',
            'tenant.phone.regex' => 'Please enter the phone number in the format XXX-XXX-XXXX.',
            'tenant.phone.max' => 'The phone number cannot exceed 12 characters.',
            'tenant.address_1.required' => 'The street address is required.',
            'tenant.address_1.max' => 'The street address cannot exceed 255 characters.',
            'tenant.address_2.max' => 'The additional address information cannot exceed 255 characters.',
            'tenant.city.required' => 'The city is required.',
            'tenant.city.max' => 'The city name cannot exceed 255 characters.',
            'tenant.state.required' => 'The state is required.',
            'tenant.state.size' => 'Please select a valid state from the dropdown.',
            'tenant.zip.required' => 'The ZIP code is required.',
            'tenant.zip.regex' => 'Please enter a valid ZIP code (e.g., 12345 or 12345-6789).',
            'tenant.zip.max' => 'The ZIP code cannot exceed 10 characters.',
        ]);

        $tenant = new Tenant;
        $tenant->account_id = $accountId;
        $tenant->first_name = $validatedData['tenant']['first_name'];
        $tenant->last_name = $validatedData['tenant']['last_name'];
        $tenant->email = $validatedData['tenant']['email'];
        $tenant->phone = $validatedData['tenant']['phone'];
        $tenant->address_1 = $validatedData['tenant']['address_1'];
        $tenant->address_2 = $validatedData['tenant']['address_2'];
        $tenant->city = $validatedData['tenant']['city'];
        $tenant->state = $validatedData['tenant']['state'];
        $tenant->zip = $validatedData['tenant']['zip'];
        $tenant->save();

        // Log the tenant creation activity during notice creation
        ActivityService::log(
            '{name} was added as a new tenant.',
            $tenant->id,
            null,
            null,
            'Tenant'
        );

        // Add the new tenant to the selected tenants array
        $this->selectedTenants[] = [
            'id' => $tenant->id,
            'name' => $tenant->full_name,
        ];

        // Track all the form data to preserve it after redirect
        $currentFormData = $this->notice;

        // Create the flash message
        session()->flash('message', 'Tenant added successfully.');
        session()->flash('messageType', 'success');

        // Store current form state in the session to preserve it across redirects
        session()->flash('form_data', [
            'notice_type_id' => $currentFormData['notice_type_id'] ?? '',
            'agent_id' => $currentFormData['agent_id'] ?? '',
            'past_due_rent' => $currentFormData['past_due_rent'] ?? 0,
            'late_charges' => $currentFormData['late_charges'] ?? 0,
            'other_1_title' => $currentFormData['other_1_title'] ?? '',
            'other_1_price' => $currentFormData['other_1_price'] ?? 0,
            'other_2_title' => $currentFormData['other_2_title'] ?? '',
            'other_2_price' => $currentFormData['other_2_price'] ?? 0,
            'other_3_title' => $currentFormData['other_3_title'] ?? '',
            'other_3_price' => $currentFormData['other_3_price'] ?? 0,
            'other_4_title' => $currentFormData['other_4_title'] ?? '',
            'other_4_price' => $currentFormData['other_4_price'] ?? 0,
            'other_5_title' => $currentFormData['other_5_title'] ?? '',
            'other_5_price' => $currentFormData['other_5_price'] ?? 0,
            'payment_other_means' => isset($currentFormData['payment_other_means']) ? (bool) $currentFormData['payment_other_means'] : false,
        ]);

        // Store selected tenants in session
        session()->flash('selected_tenants', $this->selectedTenants);

        $this->showTenantModal = false;

        // Reset the tenant form for next use
        $this->tenant = [
            'first_name' => '',
            'last_name' => '',
            'email' => '',
            'phone' => '',
            'address_1' => '',
            'address_2' => '',
            'city' => '',
            'state' => 'OR',
            'zip' => '',
        ];

        // Redirect to notices.create - all data comes from session
        return redirect()->route('notices.create');
    }

    /**
     * Add a tenant to the selected tenants list.
     *
     * @param  int  $id
     * @return void
     */
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

    /**
     * Remove a tenant from the selected tenants list.
     *
     * @param  int  $id
     * @return void
     */
    public function removeTenant($id)
    {
        $this->selectedTenants = array_filter($this->selectedTenants, function ($tenant) use ($id) {
            return $tenant['id'] != $id;
        });

        // Re-index the array
        $this->selectedTenants = array_values($this->selectedTenants);
    }
}
