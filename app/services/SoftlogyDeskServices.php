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
    
    public function getSessionToken()
    {
        $autorization=rtrim(config('services.gateway.autorization'));
        // 
        $headers = [
            'Authorization' => "Basic $autorization",
            'App-Token' =>  rtrim(config('services.gateway.app-token'))
        ];

        $endpoint="initSession";

        $session_token = $this->gatewayServices->request('GET', $endpoint, $headers);

        return $session_token;

    }

    public function reportStatusStore(array $data)
    {        
        return response()->json($data);
    }

}