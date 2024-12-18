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
    public function createEntiti(array $data)
    {

        $queryCreateUser= "INSERT INTO glpi_users (name,password,password_last_update,phone,phone2,mobile,realname,firstname,locations_id,`language`,use_mode,list_limit,is_active,comment,auths_id,authtype,last_login,date_mod,date_sync,is_deleted,profiles_id,entities_id,usertitles_id,usercategories_id,date_format,number_format,names_format,csv_delimiter,is_ids_visible,use_flat_dropdowntree,show_jobs_at_login,priority_1,priority_2,priority_3,priority_4,priority_5,priority_6,followup_private,task_private,default_requesttypes_id,password_forget_token,password_forget_token_date,user_dn,user_dn_hash,registration_number,show_count_on_tabs,refresh_views,set_default_tech,personal_token,personal_token_date,api_token,api_token_date,cookie_token,cookie_token_date,display_count_on_home,notification_to_myself,duedateok_color,duedatewarning_color,duedatecritical_color,duedatewarning_less,duedatecritical_less,duedatewarning_unit,duedatecritical_unit,display_options,is_deleted_ldap,pdffont,picture,begin_date,end_date,keep_devices_when_purging_item,privatebookmarkorder,backcreated,task_state,palette,page_layout,fold_menu,fold_search,savedsearches_pinned,timeline_order,itil_layout,richtext_layout,set_default_requester,lock_autolock_mode,lock_directunlock_notification,date_creation,highcontrast_css,plannings,sync_field,groups_id,users_id_supervisor,timezone,default_dashboard_central,default_dashboard_assets,default_dashboard_helpdesk,default_dashboard_mini_ticket,default_central_tab,nickname,timeline_action_btn_layout,timeline_date_format,use_flat_dropdowntree_on_search_result) 
        VALUES
        (
        'tienda_prueba',
        '$2y$10$7K9vaWiUTyGMbpuvjLvhVOuFADyR8E9bUpWxwFEuFJXjbAzh8Ed8i',
        '2024-12-18 10:05:03','','',
        '3115991435','','',
        {$data['']},NULL,0,NULL,1,
        '***************************
        STORE ID: 
        BRAND ID:
        ***************************
        Resolucion: 
        Prefijo Factura:
        Prefijo NotaC:
        Fecha Final:
        ***********************
        CLAVE TEC:
        73673aed9048834b741c643050d391a61e9f2194eb50bca40729ffb2c8c05125
        ***********************
        -----------------
        Resolcuion:
        Prefijo Factura:
        Prefijo NotaC:
        Fecha Final:
        ---------------------
        Clave Ténica:
        ---------------------
        Anydesk:
        Contraseña Anydesk:
        ----------------------
        Servidor Remoto:
        Contraseña Remoto:
        ----------------------
        Usuario Administrador:
        Contraseña Administrador:
        --------------------------
        Usuario SQL / Instancia:
        Contraseña SQL:
        ',0,1,NULL,'2024-12-18 13:03:44',NULL,0,1,0,393,5,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'900218148',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,'2024-12-01 12:00:00',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2024-12-18 10:05:03',0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL);";

        // Insertamos los datos
        DB::connection('mysql_second')->select($queryCreateUser);
    }
// TAG FINAL
}