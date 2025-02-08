<?php

namespace App\Livewire\Tenants;

use App\Models\Tenant;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Contracts\View\View;

class Edit extends Component
{
    public Tenant $tenant;

    public $first_name;
    public $last_name;
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
     *
     * @param  Tenant  $tenant
     * @return void
     */
    public function mount(Tenant $tenant): void
    {
        $this->tenant = $tenant;
        $this->first_name = $tenant->first_name;
        $this->last_name = $tenant->last_name;
        $this->email = $tenant->email;
        $this->phone = $tenant->phone;
        $this->address_1 = $tenant->address_1;
        $this->address_2 = $tenant->address_2;
        $this->city = $tenant->city;
        $this->state = $tenant->state;
        $this->zip = $tenant->zip;
    }

    /**
     * Update the tenant.
     *
     * @return void
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'address_1' => ['required', 'string', 'max:255'],
            'address_2' => ['nullable', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'state' => ['required', 'string', 'size:2'],
            'zip' => ['required', 'string', 'max:10'],
        ];
    }

    /**
     * Update the tenant.
     *
     * @return void
     */
    public function update(): void
    {
        $validated = $this->validate();

        $this->tenant->update($validated);

        session()->flash('message', 'Tenant successfully updated.');
        session()->flash('message-type', 'success');

        $this->redirect(route('tenants.index'), navigate: true);
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
        $this->tenant->delete();

        session()->flash('message', 'Tenant successfully deleted.');
        session()->flash('message-type', 'success');

        $this->redirect(route('tenants.index'), navigate: true);
    }

    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    #[Layout('layouts.app')]
    public function render(): View
    {
        return view('livewire.tenants.edit', [
            'states' => config('states'),
        ]);
    }
}
