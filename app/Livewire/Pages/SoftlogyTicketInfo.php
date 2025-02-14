<?php

namespace App\Livewire\Pages;

use App\Contracts\HelpDeskServicesInterface;

use Illuminate\Support\Facades\Auth;
use Livewire\WithFileUploads;

use Exception;

use Livewire\Component;

class SoftlogyTicketInfo extends Component
{
    use WithFileUploads;

    public $message = '';
    public $file;
    public $id;

    protected $listeners = ['addEmoji'];
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
            'file',
        ]);
    }
    // Upload files
    public function uploadFollowUp()
    {
        if ($this->file) {
            $path = $this->file->store('messages', 'public');
            session()->flash('message', 'Archivo cargado exitosamente: ' . $path);
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
