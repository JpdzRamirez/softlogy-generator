<?php

namespace App\Services;

use App\Contracts\SoftlogyDeskServicesInterface;

use Exception;

use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

use App\Api\Gateway;


class SoftlogyDeskServices implements SoftlogyDeskServicesInterface
{

    protected $gatewayServices;

    /**
     * Create a new class instance.
     */
    public function __construct(Gateway $gatewayServices)
    {
        $this->gatewayServices=$gatewayServices;
    }
    
    public function getAuthToken()
    {
        $autorization=rtrim(config('services.gateway.autorization'));
        // Sobrescribir headers en esta petición específica
        $headers = ['Authorization' => $autorization];

        $endpoint="initSession";

        $session_token = $this->gatewayServices->request('GET', $endpoint, $headers);

        return response()->json($session_token);

    }

    public function getTicket(int $id)
    {


    }

}