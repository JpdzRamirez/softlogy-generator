<?php

namespace App\Services;

use App\Contracts\SoftlogyDeskServicesInterface;
use App\Contracts\CastServicesInterface;

use Exception;

use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

use App\Api\Gateway;


class SoftlogyDeskServices implements SoftlogyDeskServicesInterface
{

    protected $gatewayServices;
    protected $castServices;
    protected $storeRepository;

    public function reportingArrayTransform(array $report){

        if (isset($report[1]['SQLConfig']) && is_array($report[1]['SQLConfig'])) {
            foreach ($report[1]['SQLConfig'] as $index => $item) {
                // Transformar los campos necesarios
                if (isset($item['fechaFacFin'])) {
                    $report[1]['SQLConfig'][$index]['fechaFacFin'] = $this->castServices->jsonNetFormatter($item['fechaFacFin']);
                }
    
                if (isset($item['fechaFacIni'])) {
                    $report[1]['SQLConfig'][$index]['fechaFacIni'] = $this->castServices->jsonNetFormatter($item['fechaFacIni']);
                }
    
                if (isset($item['nombre'])) {
                    $report[1]['SQLConfig'][$index]['nombre'] = $this->castServices->decodeUnicode($item['nombre']);
                }
    
                if (isset($item['direccion'])) {
                    $report[1]['SQLConfig'][$index]['direccion'] = $this->castServices->decodeUnicode($item['direccion']);
                }
            }
        }
    
        return $report;
    }
    /**
     * Create a new class instance.
     */
    public function __construct(Gateway $gatewayServices, CastServicesInterface $castServicesInterface)
    {
        $this->castServices=$castServicesInterface;
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
        return response()->json( $this->reportingArrayTransform($data));
    }

}