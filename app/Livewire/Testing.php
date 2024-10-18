<?php

namespace App\Livewire;

use Livewire\Component;

class Testing extends Component
{
    public $title = 'Initial Title';

    public function updateTitle()
    {
        $this->title = 'Updated Title';
    }

    public function render()
    {
        return view('livewire.testing');
    }
}
