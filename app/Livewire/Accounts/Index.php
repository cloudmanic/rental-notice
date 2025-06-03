<?php

namespace App\Livewire\Accounts;

use App\Models\Account;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Manage Accounts - Oregon Past Due Rent')]
class Index extends Component
{
    use WithPagination;

    /**
     * Mount the component and check permissions
     */
    public function mount()
    {
        if (auth()->user()->type !== \App\Models\User::TYPE_SUPER_ADMIN) {
            abort(403, 'You do not have permission to access this resource.');
        }
    }

    public $search = '';

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Delete an account and all its related data
     */
    public function deleteAccount($accountId)
    {
        $account = Account::findOrFail($accountId);

        // Delete all related activities
        $account->activities()->delete();

        // Delete all notices (this will also delete notice_tenant pivot records)
        $account->notices()->delete();

        // Delete all tenants
        $account->tenants()->delete();

        // Delete all agents
        $account->agents()->delete();

        // Detach all users from the account
        $account->users()->detach();

        // Delete the account
        $account->delete();

        session()->flash('message', 'Account deleted successfully.');
    }

    public function render()
    {
        $accounts = Account::query()
            ->when($this->search, function ($query) {
                return $query->where('name', 'like', '%'.$this->search.'%');
            })
            ->orderBy('name')
            ->paginate(50);

        return view('livewire.accounts.index', [
            'accounts' => $accounts,
        ])->layout('layouts.app', [
            'title' => 'Accounts',
        ]);
    }
}
