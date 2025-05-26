<?php

namespace App\Livewire\Profile;

use App\Services\ActivityService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Profile Settings - Oregon Past Due Rent')]
class Edit extends Component
{
    #[Rule('required|string|max:255')]
    public $first_name = '';

    #[Rule('required|string|max:255')]
    public $last_name = '';

    #[Rule('required|email|max:255')]
    public $email = '';

    public $current_password = '';

    #[Rule('nullable|min:8|confirmed')]
    public $password = '';

    public $password_confirmation = '';

    public function mount()
    {
        $user = Auth::user();
        $this->first_name = $user->first_name;
        $this->last_name = $user->last_name;
        $this->email = $user->email;
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.profile.edit');
    }

    public function updateProfile()
    {
        $user = Auth::user();

        // Validate email uniqueness separately since we need to exclude the current user
        $this->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$user->id,
        ]);

        $user->update([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
        ]);

        // Log the profile update activity
        ActivityService::log($this->first_name."'s profile information was updated.", null, null, null, 'User');

        // For flash message in UI
        session()->flash('message', 'Profile successfully updated.');
        session()->flash('message-type', 'success');
    }

    public function updatePassword()
    {
        $user = Auth::user();

        $this->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        // Verify current password
        if (! Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'The provided password does not match your current password.');

            return;
        }

        $user->update([
            'password' => Hash::make($this->password),
        ]);

        // Reset form fields
        $this->reset(['current_password', 'password', 'password_confirmation']);

        // Log the password update activity
        ActivityService::log($this->first_name."'s password was updated.", null, null, null, 'User');

        // For flash message in UI
        session()->flash('password_message', 'Password successfully updated.');
        session()->flash('password_message_type', 'success');
    }
}
