<?php

namespace App\Livewire\Pages;

use App\Contracts\HelpDeskServicesInterface;
use App\Contracts\CastServicesInterface;

use Illuminate\Support\Facades\Auth;
use Livewire\WithFileUploads;

use Illuminate\Validation\ValidationException;

use Exception;

use Livewire\Component;

class SoftlogyTicketInfo extends Component
{
    use WithFileUploads;

    /**
     * Services Variables
     * 
     */
    protected CastServicesInterface $castService;

    public $message = '';
    public $attach;
    public $tempFilePath;
    public $id;

    protected $listeners = ['addEmoji'];

    /**
     * 
     *   RULES
     */
    // Función rules() para generar las reglas dinámicamente
    public function rules()
    {
        // Definir las reglas base
        return [
            'message' => 'required|string|max:65535',
            'attach' =>  'nullable'
        ];
    }
    public function mount($id)
    {
        $this->id = $id;
    }
    public function addEmoji($emoji)
    {
        $this->message .= $emoji;
    }
    /**
     * RESET VALUES MODEL
     * 
     */
    public function resetAll()
    {
        $this->reset([
            'message',
            'attach',
            'tempFilePath'
        ]);
    }
    public function updatedAttach(CastServicesInterface $castService)
    {
        try {
            // Generamos base 64 para mostrar en preview
            $tempPhoto = $this->attach;
            $this->tempFilePath = $castService->processPhoto($tempPhoto);
            $this->message .= "-🎴Imagen Añadida-";
        } catch (Exception $e) {
            // Manejar error si `createTicket` falla por un error inesperado
            session()->flash('error', 'Hubo un problema al cargar la imagen | ' . $e->getMessage());
            return redirect()->route('softlogy.tickets');
        } finally {
            $this->dispatch('hideSpinnerRequest'); // Oculta el spinner
        }
    }
    // Upload files
    public function uploadFollowUp(HelpDeskServicesInterface $helpdeskServices)
    {
        try {
            $validatedData = $this->validate();

            $response = $helpdeskServices->createFollowup($this->id,  Auth::user(), $validatedData['message'], $validatedData['attach']);

            if ($response['status']) {
                $this->resetAll();
                $this->dispatch('hideSpinnerFollowup', $response);
            } else {
                throw new Exception($response['message']);
            }
        } catch (ValidationException $e) {
            // Manejar errores de validación            
            $this->dispatch('hideSpinnerRequest'); // Oculta el spinner
            throw $e;
        } catch (Exception $e) {
            session()->flash('error', $e->getMessage());
            return redirect()->route('ticket',['id'=>$this->id]);
        }
    }

    public function render(HelpDeskServicesInterface $helpdeskServices)
    {
        try {
            $response = $helpdeskServices->getTicketInfo($this->id, (int) Auth::user()->glpi_id);

            return view('livewire.pages.softlogy-ticket-info', [
                'ticketInfo' => $response,
            ]);
        } catch (Exception $e) {
            session()->flash('error', $e->getMessage());
            return redirect()->route('softlogy.tickets');
        }
    }
}
