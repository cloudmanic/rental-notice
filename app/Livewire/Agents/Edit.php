<?php

namespace App\Livewire\Agents;

use App\Models\Agent;
use App\Services\ActivityService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Edit Agent - Oregon Past Due Rent')]
class Edit extends Component
{
    public Agent $agent;

    public $name;

    public $email;

    public $phone;

    public $address_1;

    public $address_2;

    public $city;

    public $state;

    public $zip;

    public bool $showDeleteModal = false;

    /**
     * Mount the component.
     */
    public function mount(Agent $agent): void
    {
        $this->agent = $agent;
        $this->name = $agent->name;
        $this->email = $agent->email;
        $this->phone = $agent->phone;
        $this->address_1 = $agent->address_1;
        $this->address_2 = $agent->address_2;
        $this->city = $agent->city;
        $this->state = $agent->state;
        $this->zip = $agent->zip;
    }

    /**
     * Validation rules
     */
    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:12|regex:/^\d{3}-\d{3}-\d{4}$/',
            'address_1' => 'required|string|max:255',
            'address_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|size:2',
            'zip' => 'required|string|max:10|regex:/^\d{5}(-\d{4})?$/',
        ];
    }

    /**
     * Custom error messages
     */
    protected function messages()
    {
        return [
            'phone.regex' => 'Phone number must be in the format XXX-XXX-XXXX',
            'state.size' => 'State must be a 2-letter code',
            'zip.regex' => 'ZIP code must be in the format 12345 or 12345-6789',
        ];
    }

    /**
     * Update the agent.
     */
    public function update(): void
    {
        $validated = $this->validate();

        $this->agent->update($validated);

        // Log the agent update activity
        ActivityService::log('{name} agent information was updated.', null, null, $this->agent->id, 'Agent');

        session()->flash('message', 'Agent successfully updated.');
        session()->flash('message-type', 'success');

        $this->redirect(route('agents.index'), navigate: true);
    }

    public function confirmDelete(): void
    {
        $this->showDeleteModal = true;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
    }

    public function delete(): void
    {
        // Capture the agent's name before deletion
        $agentName = $this->agent->name;

        $this->agent->delete();

        // Log the agent deletion activity with explicit event type but no agent_id
        // This ensures the log persists even after the agent is deleted
        ActivityService::log("Agent $agentName was deleted.", null, null, null, 'Agent');

        session()->flash('message', 'Agent successfully deleted.');
        session()->flash('message-type', 'success');

        $this->redirect(route('agents.index'), navigate: true);
    }

    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    #[Layout('layouts.app')]
    public function render(): View
    {
        return view('livewire.agents.edit', [
            'states' => config('states'),
        ]);
    }
}
