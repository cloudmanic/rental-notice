<?php

namespace App\Livewire\Agents;

use App\Models\Agent;
use App\Services\ActivityService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Add Agent - Oregon Past Due Rent')]
class Create extends Component
{
    public $states;

    #[Rule('required|string|max:255')]
    public $name = '';

    #[Rule('nullable|string|max:255')]
    public $first_name = '';

    #[Rule('nullable|string|max:255')]
    public $last_name = '';

    #[Rule('nullable|email|max:255')]
    public $email = '';

    #[Rule('nullable|string|max:12|regex:/^\d{3}-\d{3}-\d{4}$/')]
    public $phone = '';

    #[Rule('required|string|max:255')]
    public $address_1 = '';

    #[Rule('nullable|string|max:255')]
    public $address_2 = '';

    #[Rule('required|string|max:255')]
    public $city = '';

    #[Rule('required|string|size:2')]
    public $state = 'OR'; // Default to Oregon

    #[Rule('required|string|max:10|regex:/^\d{5}(-\d{4})?$/')]
    public $zip = '';

    public function mount()
    {
        $this->states = config('states');
    }

    public function save()
    {
        $validated = $this->validate();

        $agent = new Agent;
        $agent->account_id = Auth::user()->account->id;
        $agent->fill($validated);
        $agent->save();

        // Log the agent creation activity
        ActivityService::log("Agent {$agent->name} was added.", null, null, $agent->id, 'Agent');

        session()->flash('message', 'Agent added successfully.');
        session()->flash('message-type', 'success');

        return $this->redirect(route('agents.index'));
    }

    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.agents.create');
    }

    /**
     * Get custom validation messages.
     *
     * @return array<string, string>
     */
    public static function messages()
    {
        return [
            'phone.regex' => 'Phone number must be in the format XXX-XXX-XXXX',
            'state.size' => 'State must be a 2-letter code',
            'zip.regex' => 'ZIP code must be in the format 12345 or 12345-6789',
        ];
    }
}
