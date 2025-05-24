<?php

namespace App\Livewire\Notices;

use App\Models\Notice;
use App\Models\NoticeType;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    // Search and filter properties
    public $search = '';

    public $statusFilter = '';

    public $noticeTypeFilter = '';

    public $sortField = 'created_at';

    public $sortDirection = 'desc';

    // We need to reset pagination when search params change
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingNoticeTypeFilter()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    // Method to clear all filters
    public function clearFilters()
    {
        $this->search = '';
        $this->statusFilter = '';
        $this->noticeTypeFilter = '';
        $this->resetPage();
    }

    #[Layout('layouts.app')]
    public function render()
    {
        $account = Auth::user()->account;
        $accountId = $account->id;
        $accountPlanDate = $account->notice_type_plan_date;

        $notices = Notice::where('account_id', $accountId)
            ->when($this->search, function ($query) {
                return $query->where(function ($query) {
                    $query->whereHas('noticeType', function ($q) {
                        $q->where('name', 'like', '%'.$this->search.'%');
                    })
                        ->orWhereHas('tenants', function ($q) {
                            $q->where('first_name', 'like', '%'.$this->search.'%')
                                ->orWhere('last_name', 'like', '%'.$this->search.'%')
                                ->orWhere('email', 'like', '%'.$this->search.'%');
                        })
                        ->orWhereHas('agent', function ($q) {
                            $q->where('name', 'like', '%'.$this->search.'%');
                        });
                });
            })
            ->when($this->statusFilter, function ($query) {
                return $query->where('status', $this->statusFilter);
            })
            ->when($this->noticeTypeFilter, function ($query) {
                return $query->where('notice_type_id', $this->noticeTypeFilter);
            })
            ->with(['noticeType', 'tenants', 'agent'])
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(25);

        // Get notice types for the filter dropdown - filtered by account plan date with exact match
        $noticeTypes = NoticeType::when($accountPlanDate, function ($query) use ($accountPlanDate) {
            return $query->where('plan_date', '=', $accountPlanDate);
        })->orderBy('name')->get();

        return view('livewire.notices.index', [
            'notices' => $notices,
            'noticeTypes' => $noticeTypes,
        ]);
    }
}
