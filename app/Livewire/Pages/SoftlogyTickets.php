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

    public $listTickets;

    public function mount(HelpDeskServiceInterface $helpdeskServices){
        $date = Carbon::now();
        $this->day = $date->format('d'); // Día (ej: 22)
        $this->month = $date->translatedFormat('F'); // Mes en texto (ej: Enero)
        $this->ticketsCounter=$helpdeskServices->getTicketsCount(Auth::user()->glpi_id); 
                   
    }
    public function render(HelpDeskServiceInterface $helpdeskServices)
    {           
        // Recupera los tickets con la paginación
        $ticketsList = $helpdeskServices->getTicketsUser(Auth::user()->glpi_id, 10);
        
    // Convierte la colección a un arreglo simple sin relaciones
    $this->listTickets = $ticketsList->items(); // Solo los elementos de la página actual
    // $this->total = $ticketsList->total(); // Total de tickets (necesario para paginación)
    // $this->lastPage = $ticketsList->lastPage(); // Última página
    // $this->currentPage = $ticketsList->currentPage(); // Página actual

        return view('livewire.pages.softlogy-tickets');
    }
}
