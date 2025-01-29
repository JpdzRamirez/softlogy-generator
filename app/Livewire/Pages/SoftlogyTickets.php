<?php

namespace App\Livewire\Pages;

use App\Contracts\HelpDeskServiceInterface;
use Illuminate\Support\Facades\Auth;

use Livewire\WithPagination;
use Carbon\Carbon;

use Livewire\Component;

class SoftlogyTickets extends Component
{
    use WithPagination;

    public $listTicketsId;
    public $ticketsCounter;
    public $day;
    public $month;
    public $showList = false;

    public function mount(HelpDeskServiceInterface $helpdeskServices,String $listTicketsId = "locationComponent")
    {   
        $this->listTicketsId = $listTicketsId;
        $date = Carbon::now();
        $this->day = $date->format('d'); // Día (ej: 22)
        $this->month = $date->translatedFormat('F'); // Mes en texto (ej: Enero)
        $this->ticketsCounter = $helpdeskServices->getTicketsCount(Auth::user()->glpi_id);
    }
    // Events binding
    public function toggleList()
    {
        $this->showList = !$this->showList; // Alternar estado
    }
    public function render(HelpDeskServiceInterface $helpdeskServices)
    {
        // Paginación inteligente
        $ticketsList = $helpdeskServices->getTicketsUser(Auth::user()->glpi_id, 1);
        $this->dispatch('restartToolTip');
        return view('livewire.pages.softlogy-tickets', [
            'listTickets' => $ticketsList,
        ]);
    }
}
