<?php

namespace App\Livewire\Notices;

use App\Models\Agent;
use App\Models\Notice;
use App\Models\Tenant;
use App\Models\NoticeType;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

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

    public $states;

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
        }

        // Check for flash messages from the previous request
        if (session()->has('message')) {
            $this->message = session('message');
            $this->messageType = session('messageType', 'success');
            $this->showMessage = true;
        }
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
                }
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

        $agent = new Agent();
        $agent->account_id = Auth::user()->account->id;
        $agent->name = $validatedData['agent']['name'];
        $agent->email = $validatedData['agent']['email'];
        $agent->phone = $validatedData['agent']['phone'];
        $agent->address_1 = $validatedData['agent']['address_1'];
        $agent->address_2 = $validatedData['agent']['address_2'];
        $agent->city = $validatedData['agent']['city'];
        $agent->state = $validatedData['agent']['state'];
        $agent->zip = $validatedData['agent']['zip'];
        $agent->save();

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
            'payment_other_means' => isset($currentFormData['payment_other_means']) ? (bool)$currentFormData['payment_other_means'] : false,
            'include_all_other_occupents' => isset($currentFormData['include_all_other_occupents']) ? (bool)$currentFormData['include_all_other_occupents'] : false,
        ]);

        // Redirect to notices.create - all data comes from session
        return redirect()->route('notices.create');
    }
}
