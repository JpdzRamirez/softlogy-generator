<?php

namespace App\Livewire\Pages;

use App\Contracts\HelpDeskServicesInterface;
use App\Contracts\CastServicesInterface;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

use Livewire\WithFileUploads;
use Livewire\WithPagination;

use Carbon\Carbon;
use Exception;
use Livewire\Component;

class SoftlogyTickets extends Component
{
    use WithPagination;
    use WithFileUploads;

    /**
     * Services Variables
     * 
     */
    protected CastServicesInterface $castService;
    /**
     * Summary of filter variables
     * 
     */
    public $ticketName = '';
    public $ticketStatus = '';
    public $ticketType = '';
    public $perPage = 1;

    /**
     * Toggle visibility of list tickets
     *  
     */
    public $listToggleClass;
    public $showList = false;

    /**
     * Informative variables
     *  
     */
    public $ticketsCounter;
    public $day;
    public $month;
    
    /**
     * Data of form Ticket creatión
     * 
     */
    public $ticketCheck;
    public $photoTicketData;
    public $descriptionTicketData;
    
    protected $listeners=[
        "resetPhotoTicketValue"
    ];
    
    /**
     * 
     *   RULES
     */
    protected $rules = [
        'ticketCheck' => 'required',
        'photoTicketData' => 'nullable', // Si es una URL o base64
        'descriptionTicketData' => 'nullable|string',
    ];
    public function mount(HelpDeskServicesInterface $helpdeskServices, $defaultClass = "d-none")
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

    public function updatedPhotoTicketData(CastServicesInterface $castService)
    {
        //Inicializar Instancia
        $tempPhoto = $this->photoTicketData;
        $this->photoTicketData = $castService->processPhoto($tempPhoto);
        $this->dispatch('reloadsupportModal');
    }

    // Event filter
    
    public function searchTickets()
    {
        $this->resetPage(); // Resetea la paginación al cambiar el nombre
    }  

    
    /**
     * RESET VALUES MODEL
     * 
     */
    public function resetAll()
    {
        $this->reset([
            'optionTicketCheck',
            'photoTicketData',
            'descriptionTicketData',
        ]);
    }

    public function resetPhotoTicketValue(){
        $this->photoTicketData=null;
    }

    /*
        MODAL SUBMIT FORM TICKET
    */
    public function saveTicket(HelpDeskServicesInterface $helpdeskServices)
    {   

        try{
            $validatedData = $this->validate();
            $ticketData=[
                "ticketCategory"=>$validatedData['ticketCheck'],
                "photoTicketData" =>$validatedData['photoTicketData'],
                "ticketDescription"=>$validatedData['descriptionTicketData']
            ];
            $helpdeskServices->createTicket($ticketData);
            session()->flash('status', 'Post successfully updated.');            
        }catch(ValidationException  $e){
            session()->flash('validation_errors', $e->errors());
            return redirect()->route('softlogy.tickets');
        }
    }

    /**
     *  RENDER TEMPLATE
     * 
     * 
     */
    public function render(HelpDeskServicesInterface $helpdeskServices)
    {
        // Paginación inteligente
        // Llamar al servicio con los filtros aplicados
        try{
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
        }catch(Exception $e){
            session()->flash('error', $e->getMessage());
            return redirect()->route('softlogy.tickets');
        }

    }
}
