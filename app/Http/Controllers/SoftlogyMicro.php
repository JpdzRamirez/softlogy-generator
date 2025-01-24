<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Contracts\HelpDeskServiceInterface;

use App\Models\Paises;

class SoftlogyMicro extends Controller
{   
    protected $helpdeskServices;

    // Inyección de dependencias
    public function __construct(HelpDeskServiceInterface $helpdeskServices)
    {
        $this->helpdeskServices = $helpdeskServices; // Aquí se inyecta la implementación de la interfaz
    }

    public function index(){
        return view('pages.dashboard-menu');
    }
    public function tickets(){          
        return view('pages.softlogy-tickets');
    }
    public function tools(){
        $paises = Paises::all();
        return view('pages.softlogy-tools', compact('paises'));
    }
    public function backTools(){
        $paises = Paises::all();
        return view('pages.softlogy-tools', compact('paises'));
    }
}
