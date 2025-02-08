<?php

namespace App\Livewire\Pages;

use App\Contracts\HelpDeskServicesInterface;
use App\Contracts\CastServicesInterface;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Arr;

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
    public $ticketID = '';
    public $ticketName = '';
    public $ticketStatus = '';
    public $ticketType = '';
    public $perPage = 5;

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
    public $formType;
    public $requestTitle = '';
    public $requestType = '';
    public $ticketCheck;
    public $photoTicketData;
    public $photoRequestData;
    public $descriptionTicketData;

    protected $listeners = [
        "resetPhotoTicketValue",
        "resetPhotoRequestValue",
        "resetAll"
    ];

    /**
     * 
     *   RULES
     */
    // Función rules() para generar las reglas dinámicamente
    public function rules()
    {
        // Definir las reglas base
        $rules = [                     
            'descriptionTicketData' => 'required|string|max:65535',
        ];

        // Aquí modificas las reglas en base al valor de $formType
        if ($this->formType == 1) {
            // Si el formType es 1, hacer ticketCheck obligatorio
            $rules['ticketCheck'] = 'required';
            $rules['photoTicketData'] = 'required';
            // También puedes añadir más reglas específicas para este caso
        } elseif ($this->formType == 2) {
            $rules['requestType'] = 'required';
            $rules['requestTitle'] = 'required';            
            $rules['photoRequestData'] = 'required';
        }

        return $rules;  // Devuelves las reglas dinámicas
    }
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

        try {
            //Inicializar Instancia
            $tempPhoto = $this->photoTicketData;
            $this->photoTicketData = $castService->processPhoto($tempPhoto);
            $this->dispatch('reloadsupportModal');
        } catch (Exception $e) {
            // Manejar error si `createTicket` falla por un error inesperado
            session()->flash('error', 'Hubo un problema de compatibilidad | ' . $e->getMessage());
            return redirect()->route('softlogy.tickets');
        }
    }
    public function updatedPhotoRequestData(CastServicesInterface $castService)
    {
        try {
            //Inicializar Instancia
            $tempPhoto = $this->photoRequestData;
            $this->photoRequestData = $castService->processPhoto($tempPhoto);
            $this->dispatch('reloadrequestModal');
        } catch (Exception $e) {
            // Manejar error si `createTicket` falla por un error inesperado
            session()->flash('error', 'Hubo un problema de compatibilidad | ' . $e->getMessage());
            return redirect()->route('softlogy.tickets');
        }
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
            'requestType',
            'requestTitle',
            'photoRequestData',
            'photoTicketData',
            'ticketCheck',
            'descriptionTicketData',
        ]);
    }

    public function resetPhotoTicketValue()
    {
        $this->photoTicketData = null;
    }
    public function resetPhotoRequestValue()
    {
        $this->photoRequestData = null;
    }

    /*
        MODAL SUBMIT FORM TICKET
    */
    public function saveTicket(HelpDeskServicesInterface $helpdeskServices, $formType)
    {

        try {            
            // Inicializamos la variaable
            $this->formType = $formType;
            // Validamos
            $validatedData = $this->validate();

            $ticketData = [
                "formType" => (int) $formType,
                "glpi_id" => (int) Auth::user()->glpi_id,
                "tienda" => explode('_', Auth::user()->name)[0],
                "entities_id" => Auth::user()->entities_id,
                "location_id" => Auth::user()->location_id ?? 795, 
                "descriptionTicketData" => $validatedData['descriptionTicketData'],
            ];
            
            // Agrega datos específicos según el tipo de formulario
            if ($formType == 1) {
                $ticketData += [
                    "ticketCheck" => isset($validatedData['ticketCheck']) ? (int) $validatedData['ticketCheck'] : null,
                    "photoTicketData" => $validatedData['photoTicketData'] ?? null,
                ];
            } elseif ($formType == 2) {
                $ticketData += [
                    "requestType" => isset($validatedData['requestType']) ? (int) $validatedData['requestType'] : null,
                    "requestTitle" => $validatedData['requestTitle'] ?? null,
                    "photoRequestData" => $validatedData['photoRequestData'] ?? null,
                ];
            }

            $response = $helpdeskServices->createTicket($ticketData);

            if ($response['status']) {
                $this->resetAll();
                $this->dispatch('hideSpinner', $response);
            } else {
                throw new Exception($response['message']);
            }
        } catch (ValidationException $e) {
            // Manejar errores de validación
            session()->flash('validation_errors', $e->errors() );
            return redirect()->route('softlogy.tickets');
        } catch (Exception $e) {
            // Manejar otros errores generales
            session()->flash('error', 'Hubo un problema al crear el ticket: ' . $e->getMessage());
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
        try {
            $ticketsList = $helpdeskServices->getTicketsUser(
                Auth::user()->glpi_id,
                (int)$this->ticketID,
                $this->ticketName,
                (int)$this->ticketStatus,
                (int)$this->ticketType,
                (int)$this->perPage
            );

            $this->dispatch('restartToolTip');

            return view('livewire.pages.softlogy-tickets', [
                'listTickets' => $ticketsList,
            ]);
        } catch (Exception $e) {
            session()->flash('error', $e->getMessage());
            return redirect()->route('softlogy.tickets');
        }
    }
}
