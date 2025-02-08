<?php

namespace App\Livewire\Notices;

use Livewire\Component;
use Livewire\Attributes\Layout;

class Index extends Component
{
    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.notices.index');
    }
}
