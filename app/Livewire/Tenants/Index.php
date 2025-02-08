<?php

namespace App\Livewire\Tenants;

use Livewire\Component;
use Livewire\Attributes\Layout;

class Index extends Component
{
    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.tenants.index');
    }
}
