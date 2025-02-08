<?php

namespace App\Livewire\Tenants;

use App\Models\Tenant;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Computed;

class Create extends Component
{
    #[Rule('required|string|max:255')]
    public $first_name = '';

    #[Rule('required|string|max:255')]
    public $last_name = '';

    #[Rule('nullable|email|max:255')]
    public $email = '';

    #[Rule('nullable|string|max:14|regex:/^\(\d{3}\) \d{3}-\d{4}$/')]
    public $phone = '';

    #[Rule('required|string|max:255')]
    public $address_1 = '';

    #[Rule('nullable|string|max:255')]
    public $address_2 = '';

    #[Rule('required|string|max:255')]
    public $city = '';

    #[Rule('required|string|size:2')]
    public $state = '';

    #[Rule('required|string|max:10|regex:/^\d{5}(-\d{4})?$/')]
    public $zip = '';

    public $showSuccessNotification = false;

    public array $states = [
        'AL' => 'Alabama',
        'AK' => 'Alaska',
        'AZ' => 'Arizona',
        'AR' => 'Arkansas',
        'CA' => 'California',
        'CO' => 'Colorado',
        'CT' => 'Connecticut',
        'DE' => 'Delaware',
        'FL' => 'Florida',
        'GA' => 'Georgia',
        'HI' => 'Hawaii',
        'ID' => 'Idaho',
        'IL' => 'Illinois',
        'IN' => 'Indiana',
        'IA' => 'Iowa',
        'KS' => 'Kansas',
        'KY' => 'Kentucky',
        'LA' => 'Louisiana',
        'ME' => 'Maine',
        'MD' => 'Maryland',
        'MA' => 'Massachusetts',
        'MI' => 'Michigan',
        'MN' => 'Minnesota',
        'MS' => 'Mississippi',
        'MO' => 'Missouri',
        'MT' => 'Montana',
        'NE' => 'Nebraska',
        'NV' => 'Nevada',
        'NH' => 'New Hampshire',
        'NJ' => 'New Jersey',
        'NM' => 'New Mexico',
        'NY' => 'New York',
        'NC' => 'North Carolina',
        'ND' => 'North Dakota',
        'OH' => 'Ohio',
        'OK' => 'Oklahoma',
        'OR' => 'Oregon',
        'PA' => 'Pennsylvania',
        'RI' => 'Rhode Island',
        'SC' => 'South Carolina',
        'SD' => 'South Dakota',
        'TN' => 'Tennessee',
        'TX' => 'Texas',
        'UT' => 'Utah',
        'VT' => 'Vermont',
        'VA' => 'Virginia',
        'WA' => 'Washington',
        'WV' => 'West Virginia',
        'WI' => 'Wisconsin',
        'WY' => 'Wyoming'
    ];

    public function mount()
    {
        $this->title = 'Add Tenant';
    }

    public function updatedPhone($value)
    {
        // Format phone number as (XXX) XXX-XXXX
        $cleaned = preg_replace('/[^0-9]/', '', $value);
        if (strlen($cleaned) > 0) {
            $formatted = $cleaned;
            if (strlen($cleaned) >= 3) {
                $formatted = '(' . substr($cleaned, 0, 3) . ') ' . substr($cleaned, 3);
            }
            if (strlen($cleaned) >= 6) {
                $formatted = substr($formatted, 0, 9) . '-' . substr($cleaned, 6);
            }
            $this->phone = substr($formatted, 0, 14);
        }
    }

    public function updatedState($value)
    {
        $this->state = strtoupper($value);
    }

    public function updatedZip($value)
    {
        // Format ZIP as XXXXX or XXXXX-XXXX
        $cleaned = preg_replace('/[^0-9-]/', '', $value);
        if (strlen($cleaned) > 5 && !str_contains($cleaned, '-')) {
            $this->zip = substr($cleaned, 0, 5) . '-' . substr($cleaned, 5, 4);
        } else {
            $this->zip = substr($cleaned, 0, 10);
        }
    }

    /**
     * Save the tenant to the database.
     */
    public function save()
    {
        $validated = $this->validate();

        $tenant = new Tenant();
        $tenant->account_id = Auth::user()->account->id;
        $tenant->fill($validated);
        $tenant->save();

        session()->flash('message', 'Tenant added successfully.');
        session()->flash('message-type', 'success');

        return $this->redirect(route('tenants.index'));
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.tenants.create');
    }

    public static function messages()
    {
        return [
            'phone.regex' => 'Phone number must be in the format (XXX) XXX-XXXX',
            'state.size' => 'State must be a 2-letter code',
            'zip.regex' => 'ZIP code must be in the format 12345 or 12345-6789',
        ];
    }
}
