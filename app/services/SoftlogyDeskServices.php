<?php

namespace App\Services;

use App\Contracts\SoftlogyDeskServicesInterface;
use App\Repositories\StoreConfigRepository;
use App\Repositories\SqlConfigRespository;
use App\Contracts\CastServicesInterface;
use App\Repositories\StoreRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
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
    ) {
        $this->castServices = $castServicesInterface;
        $this->gatewayServices = $gatewayServices;
        $this->storeRepository = $storeRepository;
        $this->storeConfigRepository = $storeConfigRepository;
        $this->sqlConfigRepository = $sqlConfigRepository;
    }

    public function reportingArrayTransform(array $report)
    {

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
        $autorization = rtrim(config('services.gateway.autorization'));
        // 
        $headers = [
            'Authorization' => "Basic $autorization",
            'App-Token' =>  rtrim(config('services.gateway.app-token'))
        ];

        $endpoint = "initSession";

        $session_token = $this->gatewayServices->request('GET', $endpoint, $headers);

        return $session_token;
    }

    public function reportStatusStore(array $data)
    {
        $data = $this->reportingArrayTransform($data);
        // 1. Obtener el id de usuario de la tienda autenticado
        $user = Auth::user();
        // 2. Obtener la tienda
        $store = $this->storeRepository->findByUser_Id($user->id);
        // Aseguramos que no haya límites de tiempo y memoria
        DB::statement('SET TRANSACTION ISOLATION LEVEL READ COMMITTED');
        // set_time_limit(90);
        // ini_set('default_socket_timeout', 90);
        DB::connection()->beginTransaction();
        try {
            // Validar si existe la tienda
            if ($store) {
                // Caso de que existe el store
                // Preparar los datos a actualizar solo si hay cambios
                $updatedStoreData = array_filter([
                    'entities_id' => $user->entity->id !== $store->entities_id ? $user->entity->id : null,
                    'tienda' => $data[0]['FeConfig']['Tienda'] !== $store->tienda ? $data[0]['FeConfig']['Tienda'] : null,
                    'id_tienda' => $data[0]['FeConfig']['IdTienda'] !== $store->id_tienda ? $data[0]['FeConfig']['IdTienda'] : null,
                    'id_brand' => $data[0]['FeConfig']['BrandId'] !== $store->id_brand ? $data[0]['FeConfig']['BrandId']  : null,
                    'token_user' => $data[0]['FeConfig']['TokenUser'] !== $store->token_user ? $data[0]['FeConfig']['TokenUser'] : null,
                    'token_endpoint' => $data[0]['FeConfig']['TokenEndpoint'] !== $store->token_endpoint ? $data[0]['FeConfig']['TokenEndpoint'] : null,
                    'token_pass' => $data[0]['FeConfig']['TokenPass'] !== $store->token_pass ? $data[0]['FeConfig']['TokenPass'] : null,
                    'endPoint' => $data[0]['FeConfig']['Endpoint'] !== $store->endPoint ? $data[0]['FeConfig']['Endpoint'] : null,
                    'endPoint_solicitudes' => $data[0]['FeConfig']['EndPointSolicitudes'] !== $store->endPoint_solicitudes ? $data[0]['FeConfig']['EndPointSolicitudes'] : null,
                    'endPoint_online' => $data[0]['FeConfig']['EndpointOnline'] !== $store->endPoint_online ? $data[0]['FeConfig']['EndpointOnline'] : null,
                    'endPoint_RPD' => $data[0]['FeConfig']['EndpointRPD'] !== $store->endPoint_RPD  ? $data[0]['FeConfig']['EndpointRPD'] : null,
                ], fn($value) => $value !== null); // Filtrar solo valores no nulos

                if (!empty($updatedData)) {
                    $store->update($store->id, $updatedStoreData);
                }
                if ($store->sqlConfigs) {
                    // 3. Actualizar el sqlConfig
                    foreach ($data[1]['SQLConfig'] as $sqlconfig) {

                        // Buscamos coincidencias
                        $sqlConfigFound = $this->sqlConfigRepository->findByIdPunto($sqlconfig['IdConsecutivo'], $store->id);

                        if ($sqlConfigFound) {
                            // Actualizar el sqlConfig
                            $updateSqlConfigData = array_filter([
                                'nombre' => $sqlConfigFound->nombre !== $sqlconfig['nombre'] ? $sqlconfig['nombre'] : null,
                                'razonSocial' => $sqlConfigFound->razonSocial !== $sqlconfig['razonSocial'] ? $sqlconfig['razonSocial'] : null,
                                'direccion' => $sqlConfigFound->direccion !== $sqlconfig['direccion'] ? $sqlconfig['direccion'] : null,
                                'telefono' => $sqlConfigFound->telefono !== $sqlconfig['telefono'] ? $sqlconfig['telefono'] : null,
                                'marca' => $sqlConfigFound->marca !== $sqlconfig['marca'] ? $sqlconfig['marca'] : null,
                                'numResolucion' => $sqlConfigFound->numResolucion !== $sqlconfig['numResolucion'] ? $sqlconfig['numResolucion'] : null,
                                'prefijo' => $sqlConfigFound->prefijo !== $sqlconfig['prefijo'] ? $sqlconfig['prefijo'] : null,
                                'fechaFacIni' => $sqlConfigFound->fechaFacIni !== $sqlconfig['fechaFacIni'] ? $sqlconfig['fechaFacIni'] : null,
                                'fechaFacFin' => $sqlConfigFound->fechaFacFin !== $sqlconfig['fechaFacFin'] ? $sqlconfig['fechaFacFin'] : null,
                                'rangoInicial' => $sqlConfigFound->rangoInicial !== $sqlconfig['rangoInicial'] ? $sqlconfig['rangoInicial'] : null,
                                'rangoFinal' => $sqlConfigFound->rangoFinal !== $sqlconfig['rangoFinal'] ? $sqlconfig['rangoFinal'] : null,
                                'Folio' => $sqlConfigFound->Folio !== $sqlconfig['Folio'] ? $sqlconfig['Folio'] : null,
                                'Tipo' => $sqlConfigFound->Tipo !== $sqlconfig['Tipo'] ? $sqlconfig['Tipo'] : null,
                                'NumCertificado' => $sqlConfigFound->NumCertificado !== $sqlconfig['NumCertificado'] ? $sqlconfig['NumCertificado'] : null,
                            ], fn($value) => $value !== null);

                            // Actualizar el sqlConfig
                            // Preparar los datos a actualizar solo si hay cambios
                            if (!empty($updateSqlConfigData)) {
                                $updateSqlConfigData['diferencias'] = true;
                                $this->sqlConfigRepository->update($sqlConfigFound->id, $updateSqlConfigData);
                            }
                        } else {
                            // 1. Crear el cargue con la data
                            $updateSqlConfigData = [
                                'id_consecutivo' => $sqlconfig['IdConsecutivo'] ?? null,
                                'nombre' => $sqlconfig['nombre'] ?? null,
                                'razonSocial' => $sqlconfig['razonSocial'] ?? null,
                                'store_id' => $store->id ?? null,
                                'direccion' => $sqlconfig['direccion'] ?? null,
                                'telefono' => $sqlconfig['telefono'] ?? null,
                                'marca' => $sqlconfig['marca'] ?? null,
                                'numResolucion' => $sqlconfig['numResolucion'] ?? null,
                                'prefijo' => $sqlconfig['prefijo'] ?? null,
                                'fechaFacIni' => $sqlconfig['fechaFacIni'] ?? null,
                                'fechaFacFin' => $sqlconfig['fechaFacFin'] ?? null,
                                'rangoInicial' => $sqlconfig['rangoInicial'] ?? null,
                                'rangoFinal' => $sqlconfig['rangoFinal'] ?? null,
                                'Folio' => $sqlconfig['Folio'] ?? null,
                                'Tipo' => $sqlconfig['Tipo'] ?? null,
                                'NumCertificado' => $sqlconfig['NumCertificado'] ?? null,
                            ];
                            // Crear el sqlConfig
                            $this->sqlConfigRepository->create($updateSqlConfigData);
                        }
                    }
                } else {
                    // Crear un registro por cada configuración
                    foreach ($data[1]['SQLConfig'] as $sqlconfig) {
                        // 1. Crear el sqlConfig
                        $createSqlConfigData = [
                            'id_consecutivo' => $sqlconfig['IdConsecutivo'] ?? null,
                            'nombre' => $sqlconfig['nombre'] ?? null,
                            'razonSocial' => $sqlconfig['razonSocial'] ?? null,
                            'store_id' => $store->id ?? null,
                            'direccion' => $sqlconfig['direccion'] ?? null,
                            'telefono' => $sqlconfig['telefono'] ?? null,
                            'marca' => $sqlconfig['marca'] ?? null,
                            'numResolucion' => $sqlconfig['numResolucion'] ?? null,
                            'prefijo' => $sqlconfig['prefijo'] ?? null,
                            'fechaFacIni' => $sqlconfig['fechaFacIni'] ?? null,
                            'fechaFacFin' => $sqlconfig['fechaFacFin'] ?? null,
                            'rangoInicial' => $sqlconfig['rangoInicial'] ?? null,
                            'rangoFinal' => $sqlconfig['rangoFinal'] ?? null,
                            'Folio' => $sqlconfig['Folio'] ?? null,
                            'Tipo' => $sqlconfig['Tipo'] ?? null,
                            'NumCertificado' => $sqlconfig['NumCertificado'] ?? null,
                        ];
                        // Crear el sqlConfig
                        $this->sqlConfigRepository->create($createSqlConfigData);
                    }
                }
            } else {
                //Caso de que no existe el store

                // 2. Crear el store
                $createStoredata = [
                    'user_id' => $user->id ?? null,
                    'entities_id' => $user->entity->id ?? null,
                    'tienda' => $data[0]['FeConfig']['Tienda'] ?? null,
                    'id_tienda' => $data[0]['FeConfig']['IdTienda'] ?? null,
                    'id_brand' => $data[0]['FeConfig']['BrandId'] ?? null,
                    'token_user' => $data[0]['FeConfig']['TokenUser'] ?? null,
                    'token_endpoint' => $data[0]['FeConfig']['TokenEndpoint'] ?? null,
                    'token_pass' => $data[0]['FeConfig']['TokenPass'] ?? null,
                    'endPoint' => $data[0]['FeConfig']['Endpoint'] ?? null,
                    'endPoint_solicitudes' => $data[0]['FeConfig']['EndPointSolicitudes'] ?? null,
                    'endPoint_online' => $data[0]['FeConfig']['EndpointOnline'] ?? null,
                    'endPoint_RPD' => $data[0]['FeConfig']['EndpointRPD'] ?? null,
                ];

                // Crear usuario local si no existe
                $newStore = $this->storeRepository->create($createStoredata);
                // Crear un registro por cada configuración
                foreach ($data[1]['SQLConfig'] as $sqlconfig) {
                    // 1. Crear el sqlConfig
                    $createSqlConfigData = [
                        'id_consecutivo' => $sqlconfig['IdConsecutivo'] ?? null,
                        'nombre' => $sqlconfig['nombre'] ?? null,
                        'razonSocial' => $sqlconfig['razonSocial'] ?? null,
                        'store_id' => $newStore->id ?? null,
                        'direccion' => $sqlconfig['direccion'] ?? null,
                        'telefono' => $sqlconfig['telefono'] ?? null,
                        'marca' => $sqlconfig['marca'] ?? null,
                        'numResolucion' => $sqlconfig['numResolucion'] ?? null,
                        'prefijo' => $sqlconfig['prefijo'] ?? null,
                        'fechaFacIni' => $sqlconfig['fechaFacIni'] ?? null,
                        'fechaFacFin' => $sqlconfig['fechaFacFin'] ?? null,
                        'rangoInicial' => $sqlconfig['rangoInicial'] ?? null,
                        'rangoFinal' => $sqlconfig['rangoFinal'] ?? null,
                        'Folio' => $sqlconfig['Folio'] ?? null,
                        'Tipo' => $sqlconfig['Tipo'] ?? null,
                        'NumCertificado' => $sqlconfig['NumCertificado'] ?? null,
                    ];
                    // Crear el sqlConfig
                    $this->sqlConfigRepository->create($createSqlConfigData);
                }
            }

            DB::connection()->commit();

            return response()->json(true, 201);

        } catch (Exception $e) {
            DB::connection()->rollBack();
            return response()->json(false, 404);
        }
    }
}
