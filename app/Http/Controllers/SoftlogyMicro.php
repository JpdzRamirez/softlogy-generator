<?php

namespace App\Http\Controllers;


use App\Models\Paises;

class SoftlogyMicro extends Controller
{   

    // Inyección de dependencias
    public function __construct()
    {
        
    }

    public function index(){
        return view('pages.dashboard-menu');
    }
    public function tickets(){          
        return view('pages.softlogy-tickets');
    }
    public function ticket($id){          
        return view('pages.softlogy-ticket-info',['id' => $id]);
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
