<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Contracts\HelpDeskServiceInterface;

use Exception;

class helpdeskServices implements HelpDeskServiceInterface
{

    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }
    private function curlRequest($url)
    {
        $maxRetries = 3;
        $attempts = 0;
        $responseData = null;
    
        while ($attempts < $maxRetries) {
            try {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_VERBOSE, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    
                $response = curl_exec($ch);
    
                if (curl_errno($ch)) {
                    throw new Exception('cURL Error: ' . curl_error($ch));
                }
    
                curl_close($ch);
    
                // Eliminar caracteres "&" que no son entidades HTML
                $response = preg_replace('/&(?![a-z0-9#]+;)/i', '', $response);
    
                // Decodificar JSON y verificar errores
                $responseData = json_decode($response, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new Exception('Error en la decodificación de JSON: ' . json_last_error_msg());
                }
    
                return $responseData;
    
            } catch (Exception $e) {
                error_log("Error en intento $attempts: " . $e->getMessage());
                $attempts++;
                usleep(500000); // Esperar 0.5 segundos antes de intentar nuevamente
            }
        }
    
        // Retornar null si no se pudo obtener una respuesta válida después de los intentos
        return null;
    }

    public function createLocation(array $location)
    {
        // Consulta 
        try{
        //En caso de no tener ciudad  la obtenemos de la direccion, siempre debe haber direccion
        if(empty($location['ciudad'])&&empty($location['direccion'])){
            $api_key=env('GOOGLE_KEY');
            $url="https://maps.googleapis.com/maps/api/geocode/json?address={$location['direccion']}&language=ES&key={$api_key}";           
            $ubicacion=$this->curlRequest($url);
            if ($ubicacion && $ubicacion['status'] === 'OK') {
                // Obtener el primer resultado
                $result = $ubicacion['results'][0];;
    
                foreach ($result['address_components'] as $component) {
                    if (in_array('locality', $component['types'])) {
                        $location['ciudad'] = $component['long_name'];
                    }
                    if (in_array('administrative_area_level_1', $component['types'])) {
                        $location['departamento'] = $component['long_name'];
                    }
                    if (in_array('postal_code', $component['types'])) {
                        $location['codigo_postal'] = $component['long_name'];
                    }
                }
    
            } 
        }
        $lastIdQuery="select id from glpi_users sc order by sc.id desc limit 1;";
        $lastIdResult=DB::connection('mysql_second')->select($lastIdQuery);
        // Acceder al ID y agregamos el siguiente valor
        $lastId = $lastIdResult[0]->id+1;
        $queryCreateLocation="INSERT INTO glpi_locations (entities_id,is_recursive,name,locations_id,completename,comment,`level`,ancestors_cache,sons_cache,address,postcode,town,state,country,building,room,latitude,longitude,altitude,date_mod,date_creation) 
        VALUES
            (0,0,
            '{$location['nombre']}',0,
            '{$location['direccion']}','',1,'[]',
            '{'{$lastId}':{$lastId}}',
            '{$location['direaccion']}, {$location['ciudad']}, {$location['departamento']}',
            '{$location['codigo_postal']}',
            '{$location['ciudad']}',
            '{$location['departamento']}',
            'Colombia','','','','','','2024-12-18 12:46:53','2024-12-18 12:46:53');"; 
    
        // Retorna un arreglo vacío si el archivo falla
        // Ejecutar la consulta
        DB::connection('mysql_second')->insert($queryCreateLocation);

        // Obtener el último ID insertado
        $lastInsertedId = DB::connection('mysql_second')->getPdo()->lastInsertId();

        return $lastInsertedId;

        }catch (Exception $e) {
            return   "ERROR DE INTEGRACION".$e->getMessage();
        }
    }
// TAG FINAL
}