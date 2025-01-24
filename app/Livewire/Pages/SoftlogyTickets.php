<?php

namespace App\Livewire\Pages;

use App\Contracts\HelpDeskServiceInterface;
use Illuminate\Support\Facades\Auth;

use Carbon\Carbon;

use Livewire\Component;

class SoftlogyTickets extends Component
{   
    public $ticketsCounter;
    public $day;
    public $month;

    public function mount(HelpDeskServiceInterface $helpdeskServices){
        $date = Carbon::now();
        $this->day = $date->format('d'); // Día (ej: 22)
        $this->month = $date->translatedFormat('F'); // Mes en texto (ej: Enero)
        $this->ticketsCounter=$helpdeskServices->getTicketsCount(Auth::user()->glpi_id);          
    }
    public function render()
    {
        return view('livewire.pages.softlogy-tickets');
    }
}
