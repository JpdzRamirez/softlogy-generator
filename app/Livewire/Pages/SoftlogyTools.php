<?php

namespace App\Livewire\Pages;

use Livewire\Component;

class SoftlogyTools extends Component
{   
    public $paises;
    public function mount($paises = []){
        $this->paises=$paises;
    }
    public function render()
    {
        return view('livewire.pages.softlogy-tools');
    }
}
