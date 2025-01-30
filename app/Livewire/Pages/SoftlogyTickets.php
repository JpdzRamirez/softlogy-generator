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

    public $ticketName = '';
    public $ticketStatus = '';
    public $ticketType = '';
    public $perPage = 1;
    public $listToggleClass;
    public $ticketsCounter;
    public $day;
    public $month;
    public $showList = false;

    public function mount(HelpDeskServiceInterface $helpdeskServices, $defaultClass = "d-none")
    {
        $date = Carbon::now();
        $this->day = $date->format('d'); // Día (ej: 22)
        $this->month = $date->translatedFormat('F'); // Mes en texto (ej: Enero)
        $this->ticketsCounter = $helpdeskServices->getTicketsCount(Auth::user()->glpi_id);
        $this->listToggleClass = $defaultClass;
    }
    // Events binding
    public function toggleList()
    {
        $this->showList = !$this->showList; // Alternar estado
        if ($this->showList) {
            $this->listToggleClass = "listTicketToggled";
        } else {
            $this->listToggleClass = "listTicketNotToggled";
            $this->dispatch('hideListTickets');
        }
    }
    // Event filter
    public function searchTickets()
    {
        $this->resetPage(); // Resetea la paginación al cambiar el nombre
    }
    public function render(HelpDeskServiceInterface $helpdeskServices)
    {
        // Paginación inteligente
        // Llamar al servicio con los filtros aplicados
        $ticketsList = $helpdeskServices->getTicketsUser(
            Auth::user()->glpi_id,
            $this->ticketName,
            $this->ticketStatus,
            $this->ticketType,
            $this->perPage
        );

        $this->dispatch('restartToolTip');

        return view('livewire.pages.softlogy-tickets', [
            'listTickets' => $ticketsList,
        ]);
    }
}
