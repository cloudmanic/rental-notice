<?php

namespace App\Livewire\Realtors;

use App\Models\RealtorList;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Realtors - Oregon Past Due Rent')]
class Index extends Component
{
    use WithPagination;

    public $search = '';

    public $sortField = 'full_name';

    public $sortDirection = 'asc';

    public $showColumnSelector = false;

    // Available columns for selection
    public $availableColumns = [
        'csv_id' => 'ID',
        'full_name' => 'Full Name',
        'email' => 'Email',
        'office_name' => 'Office Name',
        'city' => 'City',
        'state' => 'State',
        'county' => 'County',
        'phone' => 'Phone',
        'mobile' => 'Mobile',
        'license_type' => 'License Type',
        'license_number' => 'License Number',
        'expiration_date' => 'License Expiration',
        'association' => 'Association',
        'agency' => 'Agency',
        'listings' => 'Listings',
        'listings_volume' => 'Listings Volume',
        'sold' => 'Sold',
        'sold_volume' => 'Sold Volume',
        'email_status' => 'Email Status',
    ];

    // Default visible columns
    public $selectedColumns = [
        'full_name',
        'email',
        'office_name',
        'city',
        'state',
        'phone',
        'license_number',
        'expiration_date',
    ];

    protected $queryString = [
        'search' => ['except' => ''],
        'sortField' => ['except' => 'full_name'],
        'sortDirection' => ['except' => 'asc'],
    ];

    /**
     * Initialize component
     */
    public function mount()
    {
        // Check if user is super admin
        if (auth()->user()->type !== User::TYPE_SUPER_ADMIN) {
            abort(403);
        }

        // Load saved column preferences from session
        $savedColumns = session('realtor_columns');
        if ($savedColumns && is_array($savedColumns)) {
            $this->selectedColumns = $savedColumns;
        }
    }

    /**
     * Reset pagination when search changes
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Toggle column visibility
     */
    public function toggleColumn($column)
    {
        if (in_array($column, $this->selectedColumns)) {
            $this->selectedColumns = array_values(array_diff($this->selectedColumns, [$column]));
        } else {
            $this->selectedColumns[] = $column;
        }

        // Save to session
        session(['realtor_columns' => $this->selectedColumns]);
    }

    /**
     * Sort by field
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

    /**
     * Toggle column selector visibility
     */
    public function toggleColumnSelector()
    {
        $this->showColumnSelector = ! $this->showColumnSelector;
    }

    /**
     * Export to CSV
     */
    public function export()
    {
        $this->redirect(route('realtors.export'));
    }

    /**
     * Get the query builder
     */
    private function getQuery()
    {
        $query = RealtorList::query();

        // Apply search
        if ($this->search) {
            $searchTerm = '%'.$this->search.'%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('full_name', 'like', $searchTerm)
                    ->orWhere('email', 'like', $searchTerm)
                    ->orWhere('office_name', 'like', $searchTerm)
                    ->orWhere('city', 'like', $searchTerm)
                    ->orWhere('county', 'like', $searchTerm)
                    ->orWhere('phone', 'like', $searchTerm)
                    ->orWhere('mobile', 'like', $searchTerm)
                    ->orWhere('license_number', 'like', $searchTerm)
                    ->orWhere('association', 'like', $searchTerm)
                    ->orWhere('agency', 'like', $searchTerm);
            });
        }

        // Apply sorting
        $query->orderBy($this->sortField, $this->sortDirection);

        return $query;
    }

    /**
     * Render the component
     */
    #[Layout('layouts.app')]
    public function render()
    {
        $realtors = $this->getQuery()->paginate(100);

        return view('livewire.realtors.index', [
            'realtors' => $realtors,
        ]);
    }
}
