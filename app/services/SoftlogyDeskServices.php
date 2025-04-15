<?php

namespace App\Services;

use App\Contracts\SoftlogyDeskServicesInterface;
use App\Repositories\StoreConfigRepository;
use App\Repositories\SqlConfigRespository;
use App\Contracts\CastServicesInterface;
use App\Repositories\StoreRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Exception;

use App\Api\Gateway;


class SoftlogyDeskServices implements SoftlogyDeskServicesInterface
{

    protected $gatewayServices;
    protected $castServices;
    protected $storeRepository;
    protected $storeConfigRepository;
    protected $sqlConfigRepository;

    /**
     * Create a new class instance.
     */
    public function __construct(
        Gateway $gatewayServices, 
        CastServicesInterface $castServicesInterface,
        StoreRepository $storeRepository,
        StoreConfigRepository $storeConfigRepository,
        SqlConfigRespository $sqlConfigRepository
        )
    {
        $this->castServices=$castServicesInterface;
        $this->gatewayServices=$gatewayServices;
        $this->storeRepository=$storeRepository;
        $this->storeConfigRepository=$storeConfigRepository;
        $this->sqlConfigRepository=$sqlConfigRepository;        
    }
    
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
        $data=$this->reportingArrayTransform($data);

        $store=$this->storeRepository->findByUser_Id(Auth::user()->id);

        if($store){
            // Caso de que existe el store
             // Preparar los datos a actualizar solo si hay cambios
             $updatedStoreData = array_filter([                                
                'tienda' => $data[0]['FeConfig'][0]['Tienda'] !== $store->tienda ? $data[0]['FeConfig'][0]['Tienda'] : null,
                'id_tienda' => $data[0]['FeConfig'][0]['id_tienda'] !== $store->id_tienda ? $data[0]['FeConfig'][0]['id_tienda'] : null,
                'id_brand' => $data[0]['FeConfig'][0]['id_brand'] !== $store->id_brand ? $data[0]['FeConfig'][0]['id_brand']  : null,
                'token_User' => $data[0]['FeConfig'][0]['token_User'] !== $store->token_User ? $data[0]['FeConfig'][0]['token_User'] : null,
                'token_endpoint' => $data[0]['FeConfig'][0]['token_endpoint'] !== $store->token_endpoint ? $data[0]['FeConfig'][0]['token_endpoint'] : null,
                'token_pass' => $data[0]['FeConfig'][0]['token_pass'] !== $store->token_pass ? $data[0]['FeConfig'][0]['token_pass'] : null,
                'endPoint' => $data[0]['FeConfig'][0]['endPoint'] !== $store->endPoint ? $data[0]['FeConfig'][0]['endPoint'] : null,
                'endPoint_solicitudes' => $data[0]['FeConfig'][0]['endPoint_solicitudes'] !== $store->endPoint_solicitudes ? $data[0]['FeConfig'][0]['endPoint_solicitudes']: null,
                'endPoint_online' => $data[0]['FeConfig'][0]['endPoint_online'] !== $store->endPoint_solicitudes ? $data[0]['FeConfig'][0]['endPoint_online'] : null,
                'endPoint_RPD' => $data[0]['FeConfig'][0]['endPoint_RPD'] !== $store->endPoint_RPD  ? $data[0]['FeConfig'][0]['endPoint_RPD'] : null,                
            ], fn($value) => $value !== null); // Filtrar solo valores no nulos

            if (!empty($updatedData)) {
                $store->update($store->id, $updatedStoreData);
            }

        }else{
            //Caso de que no existe el store
            
            // 2. Crear el store
                $createStoredata = [
                    'user_id' => $data[0]['FeConfig'][0]['Tienda'] ?? null,
                    'entitie_id' => $data[0]['FeConfig'][0]['Tienda'] ?? null,                                
                    'tienda' => $data[0]['FeConfig'][0]['id_tienda'] ?? null,
                    'id_tienda' => $data[0]['FeConfig'][0]['id_tienda'] ?? null,
                    'id_brand' => $data[0]['FeConfig'][0]['id_brand'] ?? null,
                    'token_User' => $data[0]['FeConfig'][0]['token_User'] ?? null,
                    'token_endpoint' => $data[0]['FeConfig'][0]['token_endpoint'] ?? null,
                    'token_pass' => $data[0]['FeConfig'][0]['token_pass'] ?? null,
                    'endPoint' => $data[0]['FeConfig'][0]['endPoint'] ?? null,
                    'endPoint_solicitudes' => $data[0]['FeConfig'][0]['endPoint_solicitudes'] ?? null,
                    'endPoint_online' => $data[0]['FeConfig'][0]['endPoint_online'] ?? null,
                    'endPoint_RPD' => $data[0]['FeConfig'][0]['endPoint_RPD'] ?? null,                
                ];
                        
            // Crear usuario local si no existe
                $newStore = $this->storeRepository->create($data);
            // Crear un registro por cada configuración
            foreach( $data[1]['SQLConfig'] as $sqlconfig){
                // 1. Crear el sqlConfig
                $createStoredata = [
                    'nombre' => $sqlconfig->nombre ?? null,
                    'store_id' =>  $newStore->id ?? null,
                    'direccion' => $sqlconfig->direccion ?? null,                
                    'telefono' => $sqlconfig->telefono ?? null,   
                    'marca' => $sqlconfig->marca ?? null, 
                    'numResolucion' => $sqlconfig->numResolucion ?? null, 
                    'prefijo' => $sqlconfig->prefijo ?? null, 
                    'fechaFacIni' => $sqlconfig->fechaFacIni ?? null, 
                    'fechaFacFin' => $sqlconfig->fechaFacFin ?? null, 
                    'rangoInicial' => $sqlconfig->rangoInicial ?? null, 
                    'rangoFinal' => $sqlconfig->rangoFinal ?? null, 
                    'Folio' => $sqlconfig->Folio ?? null, 
                    'Tipo' => $sqlconfig->Tipo ?? null, 
                    'NumCertificado' => $sqlconfig->NumCertificado ?? null, 
                ];
                $sqlConfig = $this->sqlConfigRepository->create($createStoredata);
            }
            

        }

        return response()->json( $data);
    }

}
