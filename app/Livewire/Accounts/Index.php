<?php

namespace App\Livewire\Accounts;

use App\Models\Account;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
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
