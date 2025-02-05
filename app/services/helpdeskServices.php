<?php

namespace app\services;

use Illuminate\Support\Facades\DB;
use App\Contracts\HelpDeskServicesInterface;


use App\Repositories\GlpiTicketsRepository;

use Exception;

class HelpdeskServices implements HelpDeskServicesInterface
{

    protected $glpiTicketRepository;
    
    /**
     * Create a new class instance.
     */
    public function __construct(GlpiTicketsRepository $glpiTicketRepository)
    {
        $this->glpiTicketRepository = $glpiTicketRepository;        
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
        // Aseguramos que no haya límites de tiempo y memoria
        set_time_limit(0);  // Sin límite de tiempo de ejecución
        ini_set("memory_limit", -1);  // Sin límite de memoria
        // Iniciar la transacción
        DB::connection('mysql_second')->beginTransaction();
        // Consulta 
        try {
            //En caso de no tener ciudad  la obtenemos de la direccion, siempre debe haber direccion
            if (empty($location['ciudad']) || $location['ciudad'] == '#N/A') {
                $api_key = env('GOOGLE_KEY');
                $address = $location['direccion'];
                $encodedAddress = urlencode($address);
                $url = "https://maps.googleapis.com/maps/api/geocode/json?address={$encodedAddress}&language=ES&key={$api_key}";
                $ubicacion = $this->curlRequest($url);
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
            //Obtenemos el ultimo ID de locations
            $lastIdResult = DB::connection('mysql_second')
                ->table('glpi_locations')
                ->orderBy('id', 'desc')
                ->limit(1)
                ->lockForUpdate() // Bloqueamos la fila seleccionada para evitar que otros la modifiquen
                ->value('id');
            // Acceder al ID y agregamos el siguiente valor
            $lastId = $lastIdResult + 1;
            //Casting variables
            $ancestorsCache = json_encode([$lastId => $lastId]);
            $nombreDireccion = trim($location['nombre']); // Eliminar espacios al principio y al final
            $nombreDireccion = str_replace(' ', '_', $nombreDireccion); // Reemplazar los espacios por _
            $queryCreateLocation = "INSERT INTO glpi_locations (entities_id,is_recursive,name,locations_id,completename,comment,`level`,ancestors_cache,sons_cache,address,postcode,town,state,country,building,room,latitude,longitude,altitude,date_mod,date_creation) 
        VALUES
            (0,1,
            '{$nombreDireccion}',0,
            '{$location['direccion']}','',1,'[]',
            '{$ancestorsCache}',
            '{$location['direccion']}, {$location['ciudad']}, {$location['departamento']}',
            '{$location['codigo_postal']}',
            '{$location['ciudad']}',
            '{$location['departamento']}',
            'Colombia','','','','','','2024-12-18 12:46:53','2024-12-18 12:46:53');";

            // Retorna un arreglo vacío si el archivo falla
            // Ejecutar la consulta
            DB::connection('mysql_second')->insert($queryCreateLocation);

            // Obtener el último ID insertado
            $lastInsertedId =  DB::connection('mysql_second')
                ->table('glpi_locations')
                ->orderBy('id', 'desc')
                ->limit(1)
                ->value('id');

            $ancestorsCache = json_encode([$lastInsertedId => $lastInsertedId]);

            $updateSonCache = "UPDATE glpi_locations
        SET sons_cache='{$ancestorsCache}'
        WHERE id={$lastInsertedId};";

            // Ejecutar la consulta
            DB::connection('mysql_second')->update($updateSonCache);

            // Confirmar la transacción si todo fue bien
            DB::connection('mysql_second')->commit();

            return $lastInsertedId;
        } catch (Exception $e) {
            // Si ocurre un error, revertir los cambios
            DB::connection('mysql_second')->rollBack();
            throw $e;
        }
    }
    public function createEntiti(array $data)
    {
        // Aseguramos que no haya límites de tiempo y memoria
        set_time_limit(0);  // Sin límite de tiempo de ejecución
        ini_set("memory_limit", -1);  // Sin límite de memoria

        // Iniciar la transacción
        DB::connection('mysql_second')->beginTransaction();

        try {
            // Uso de lockForUpdate() para bloquear registros mientras se hacen operaciones
            // Esto se aplica para realizar una consulta sobre un registro que no se desea que otras peticiones cambien
            $lastIdResult = DB::connection('mysql_second')
                ->table('glpi_users')
                ->orderBy('id', 'desc')
                ->limit(1)
                ->lockForUpdate()  // Aquí bloqueamos la fila para que no sea modificada mientras estamos en la transacción
                ->value('id');

            // Incrementamos el último ID obtenido
            $lastId = $lastIdResult + 1;

            // Preparar el primer query de inserción para crear el usuario
            $queryCreateUser = "INSERT INTO glpi_users (name,password,password_last_update,phone,phone2,mobile,realname,firstname,locations_id,`language`,use_mode,list_limit,is_active,comment,auths_id,authtype,last_login,date_mod,date_sync,is_deleted,profiles_id,entities_id,usertitles_id,usercategories_id,date_format,number_format,names_format,csv_delimiter,is_ids_visible,use_flat_dropdowntree,show_jobs_at_login,priority_1,priority_2,priority_3,priority_4,priority_5,priority_6,followup_private,task_private,default_requesttypes_id,password_forget_token,password_forget_token_date,user_dn,user_dn_hash,registration_number,show_count_on_tabs,refresh_views,set_default_tech,personal_token,personal_token_date,api_token,api_token_date,cookie_token,cookie_token_date,display_count_on_home,notification_to_myself,duedateok_color,duedatewarning_color,duedatecritical_color,duedatewarning_less,duedatecritical_less,duedatewarning_unit,duedatecritical_unit,display_options,is_deleted_ldap,pdffont,picture,begin_date,end_date,keep_devices_when_purging_item,privatebookmarkorder,backcreated,task_state,palette,page_layout,fold_menu,fold_search,savedsearches_pinned,timeline_order,itil_layout,richtext_layout,set_default_requester,lock_autolock_mode,lock_directunlock_notification,date_creation,highcontrast_css,plannings,sync_field,groups_id,users_id_supervisor,timezone,default_dashboard_central,default_dashboard_assets,default_dashboard_helpdesk,default_dashboard_mini_ticket,default_central_tab,nickname,timeline_action_btn_layout,timeline_date_format,use_flat_dropdowntree_on_search_result) 
                VALUES
                (
                    '{$data['usuario']}',
                    '$2y$10$7K9vaWiUTyGMbpuvjLvhVOuFADyR8E9bUpWxwFEuFJXjbAzh8Ed8i',
                    '2024-12-18 10:05:03','','',
                    '{$data['telefono']}','','',
                    {$data['location_id']},NULL,0,NULL,1,
                    '***************************
                    STORE ID: {$data['store_id']}
                    BRAND ID: {$data['brand_id']}
                    ***************************
                    Resolucion: {$data['resol_1']}
                    Prefijo Factura:{$data['prefijo_fact_1']}
                    Prefijo NotaC: {$data['prefijo_nota_1']}
                    Fecha Final: {$data['fecha_resol_1']}
                    ***********************
                    CLAVE TEC:
                    {$data['clave_tecnica']}
                    ***********************
                    -----------------
                    Resolcuion: {$data['resol_2']}
                    Prefijo Factura: {$data['prefijo_fact_2']}
                    Prefijo NotaC: {$data['prefijo_nota_2']}
                    Fecha Final: {$data['fecha_resol_2']}
                    ---------------------
                    Conexiones
                    ---------------------
                    Anydesk:{$data['anydesk']}
                    Contraseña Anydesk: {$data['contrasena']}
                    ----------------------
                    Servidor Remoto: 
                    Contraseña Remoto:
                    ----------------------
                    Usuario Administrador:
                    Contraseña Administrador:
                    --------------------------
                    Usuario SQL / Instancia:
                    Contraseña SQL:
                    ',0,1,NULL,'2024-12-18 13:03:44',NULL,0,1,
                    20,
                    393
                    ,5,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,
                    '{$data['nit']}',
                    NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,'2024-12-01 12:00:00',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2024-12-18 10:05:03',0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL);";

            // Insertamos los datos del usuario
            DB::connection('mysql_second')->insert($queryCreateUser);

            // Obtener el último ID de usuario insertado
            $lastIdResult = DB::connection('mysql_second')
                ->table('glpi_users')
                ->orderBy('id', 'desc')
                ->limit(1)
                ->value('id');

            // Realizar la segunda inserción con el correo electrónico
            $queryCreateEmail = "INSERT INTO glpi_useremails (users_id,is_default,is_dynamic,email) 
                VALUES
                ({$lastId},1,0,'{$data['correo']}');";


            // Insertar el email
            DB::connection('mysql_second')->insert($queryCreateEmail);

            // Realizar la Tercera inserción en perfiles relacionando la entidad, entidad, perfil etc
            $queryEntitiRelation = "INSERT INTO glpi_profiles_users (users_id,profiles_id,entities_id,is_recursive,is_dynamic,is_default_profile) 
            VALUES
	        ({$lastId},1,
            20,
            0,0,0);";

            // Insertar la relacion
            DB::connection('mysql_second')->insert($queryEntitiRelation);

            // Confirmar la transacción si todo fue bien
            DB::connection('mysql_second')->commit();

            return TRUE;
        } catch (Exception $e) {
            // Si ocurre un error, revertir los cambios
            DB::connection('mysql_second')->rollBack();
            throw $e;
        }
    }
    public function getTicketsCount(int $idUser)
    {
        try {
            $tickets = [
                "nuevos" => 0,
                "encurso" => 0,
                "planificados" => 0,
                "enespera" => 0,
                "solucionados" => 0,
                "cerrados" => 0
            ];
            // Obtenemos el numero de tickets del usuario
            $statusCounts = DB::connection('mysql_second')
                ->table('glpi_tickets')
                ->join('glpi_tickets_users', 'glpi_tickets.id', '=', 'glpi_tickets_users.tickets_id')
                ->select('glpi_tickets.status', DB::raw('COUNT(*) as total'))
                ->where('glpi_tickets_users.users_id', $idUser)
                ->groupBy('glpi_tickets.status')
                ->orderBy('glpi_tickets.status')
                ->get();

            // Iterar sobre los resultados de la consulta para asignar los valores al arreglo
            foreach ($statusCounts as $statusCount) {
                switch ($statusCount->status) {
                    case '1':
                        $tickets["nuevos"] = $statusCount->total;
                        break;
                    case '2':
                        $tickets["encurso"] = $statusCount->total;
                        break;
                    case '3':
                        $tickets["planificados"] = $statusCount->total;
                        break;
                    case '4':
                        $tickets["enespera"] = $statusCount->total;
                        break;
                    case '5':
                        $tickets["solucionados"] = $statusCount->total;
                        break;
                    case '6':
                        $tickets["cerrados"] = $statusCount->total;
                        break;
                }
            }
            return $tickets;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function getTicketsUser(int $idUser,int $ticketID,string $ticketName,int $ticketStatus, int $ticketType, int $perPage)
    {
        // Consulta 
        try {
            $ticketsList = $this->glpiTicketRepository->getAllTicketsUser(
                $idUser,
                $ticketID,
                $ticketName,
                $ticketStatus,
                $ticketType,
                $perPage
            );

            if (!$ticketsList) {
                return null;
            }

            // Iteramos cada ticket
            foreach ($ticketsList as $ticket) {

                $recipients = $this->glpiTicketRepository->getAllRecipients($ticket->id);

                // Verificar quienes son los actores del caso
                if ($recipients) {
                    // Inicializar las variables para técnicos y observadores
                    $tecnicos = [];
                    $observadores = [];
                    $observadoresTooltip = null;
                    // Recorrer los destinatarios y asignarlos a la propiedad correspondiente
                    foreach ($recipients as $recipient) {
                        // Comprobar el tipo de destinatario
                        if ($recipient->type == 2) {
                            // Agregar a los técnicos
                            $nombres = explode(" ", $recipient->firstname);
                            $apellidos = explode(" ", $recipient->realname);
                            $tecnicos[] = $nombres[0] . " " . $apellidos[0];
                        } elseif ($recipient->type == "3") {
                            // Agregar a los observadores
                            $nombres = explode(" ", $recipient->firstname);
                            $apellidos = explode(" ", $recipient->realname);
                            $observadores[] = $nombres[0] . " " . $apellidos[0];
                        }
                    }
                    // Crear el tooltip para observadores
                    if (!empty($observadores)) {
                        $observadoresTooltip = implode(', ', $observadores);
                    }
                    // Asignar los resultados a las propiedades dinámicas del ticket
                    $ticket->tecnicos = $tecnicos;
                    $ticket->observadores = $observadoresTooltip;
                }
                // Procesar contenido del ticket principal
                $decodedContent = htmlspecialchars_decode($ticket->content);
                // Extraer texto limpio
                $textCleaned = strip_tags($decodedContent);

                $ticket->content = $textCleaned;

                $imagesBase64 = [];

                foreach ($ticket->documents as $document) {
                    $resoursesFile = env('SOFTLOGY_HELDEKS_RESOURCES');
                    $completePath = $resoursesFile . DIRECTORY_SEPARATOR . $document->filepath;

                    if (file_exists($completePath)) {
                        $imageData = file_get_contents($completePath);
                        $imagesBase64[] = 'data:' . $document->mime . ';base64,' . base64_encode($imageData);
                    }
                }
                if (!empty($imagesBase64)) {
                    $ticket->resources = $imagesBase64;
                }
            }

            // Devuelve la respuesta estructurada
            return $ticketsList;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function createTicket(array $ticketData)
    {

        // Aseguramos que no haya límites de tiempo y memoria
        DB::statement('SET TRANSACTION ISOLATION LEVEL READ COMMITTED');
        // set_time_limit(90);
        // ini_set('default_socket_timeout', 90);
        DB::connection('mysql_second')->beginTransaction();
        try {
            $ticket = $this->glpiTicketRepository->createTicket($ticketData);
            $this->glpiTicketRepository->createRelationTicketUser($ticket,$ticketData);
            $content = $this->glpiTicketRepository->glpiContenTicketBuilder($ticketData,$ticket);
            $this->glpiTicketRepository->updateTicket($ticket->id,$content);
            DB::connection('mysql_second')->commit();
            return [
                'ticket'=>$ticket->id,
                'status'=>true
            ];
        } catch (Exception $e) {
            DB::connection('mysql_second')->rollBack();
            return [
                'message' => "Error en la transacción: " . $e->getMessage(),
                'status' => false
            ];
        }
    }
}
