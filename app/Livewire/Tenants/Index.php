<?php

namespace App\Livewire\Tenants;

use App\Models\Tenant;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Tenants - Oregon Past Due Rent')]
class Index extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public string $search = '';

    #[Url(history: true)]
    public string $sortField = 'last_name';

    #[Url(history: true)]
    public string $sortDirection = 'asc';

    /**
     * Reset the search field.
     */
    public function updatedSearch()
    {
        $this->resetPage();
    }

    /**
     * Sort the tenants by the given field.
     *
     * @param  string  $field
     */
    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function clearMessage()
    {
        session()->forget(['message', 'message-type']);
    }

    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    #[Layout('layouts.app')]
    public function render()
    {
        $tenants = Tenant::query()
            ->where('account_id', Auth::user()->account->id)
            ->where(function ($query) {
                $query->where('first_name', 'like', '%'.$this->search.'%')
                    ->orWhere('last_name', 'like', '%'.$this->search.'%')
                    ->orWhere('address_1', 'like', '%'.$this->search.'%')
                    ->orWhere('city', 'like', '%'.$this->search.'%')
                    ->orWhere('state', 'like', '%'.$this->search.'%')
                    ->orWhere('zip', 'like', '%'.$this->search.'%');
            })
            ->when($this->sortField === 'name', function ($query) {
                $query->orderBy('last_name', $this->sortDirection)
                    ->orderBy('first_name', $this->sortDirection);
            }, function ($query) {
                $query->orderBy($this->sortField, $this->sortDirection);
            })
            ->paginate(25);

        return view('livewire.tenants.index', [
            'tenants' => $tenants,
            'message' => session('message'),
            'messageType' => session('message-type'),
        ]);
    }
}
