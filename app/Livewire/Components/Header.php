<?php

namespace App\Livewire\Components;
use Illuminate\Support\Facades\Auth;

use Livewire\Component;

class Header extends Component
{   
    protected $listeners= [
        'confirmLogout'=>'logout'
    ];
    public function logout()
    {
        Auth::guard('web')->logout();
        session()->invalidate(); 
        session()->regenerateToken(); 
        return redirect()->route('home');
    }
    public function render()
    {
        return view('livewire.components.header');
    }
}
