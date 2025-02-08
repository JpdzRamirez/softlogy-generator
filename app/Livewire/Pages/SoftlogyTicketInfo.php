<?php

namespace App\Livewire\Pages;

use App\Contracts\HelpDeskServicesInterface;

use Illuminate\Support\Facades\Auth;

use Livewire\Component;

class SoftlogyTicketInfo extends Component
{
    public $id;

    public function mount($id)
    {
        $this->id = $id;
        
    }

    public function render(HelpDeskServicesInterface $helpdeskServices)
    {
        $response=$helpdeskServices->getTicketInfo($this->id,(int) Auth::user()->glpi_id);
        
        return view('livewire.pages.softlogy-ticket-info');
    }
}
