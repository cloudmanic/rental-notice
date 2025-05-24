<?php

namespace App\Livewire\Account;

use App\Models\Account;
use App\Services\ActivityService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Edit extends Component
{
    public Account $account;

    public $name;

    public bool $showDeleteModal = false;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        // Get the current user's account
        $this->account = Auth::user()->account;
        $this->name = $this->account->name;
    }

    /**
     * Update the account.
     */
    public function update(): void
    {
        // Validate the form data
        $validated = $this->validate([
            'name' => 'required|string|max:255',
        ]);

        // Update the account
        $this->account->update($validated);

        // Log the account update activity
        ActivityService::log("Account name was updated to {$this->name}.", null, null, null, 'Account');

        // For flash message in UI
        session()->flash('message', 'Account successfully updated.');
        session()->flash('message-type', 'success');
    }

    /**
     * Show the delete confirmation modal.
     */
    public function confirmDelete(): void
    {
        $this->showDeleteModal = true;
    }

    /**
     * Cancel the delete operation and hide the modal.
     */
    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
    }

    /**
     * Delete the account.
     */
    public function delete(): void
    {
        // Delete the account (this will cascade delete related records due to foreign key constraints)
        $this->account->delete();

        // Redirect to login page with a flash message
        session()->flash('message', 'Account successfully deleted.');
        session()->flash('message-type', 'success');

        $this->redirect(route('login'));
    }

    /**
     * Render the component.
     */
    #[Layout('layouts.app')]
    public function render(): View
    {
        return view('livewire.account.edit');
    }
}
