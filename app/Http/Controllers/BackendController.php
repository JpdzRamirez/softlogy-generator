<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

use App\Contracts\HelpDeskServiceInterface;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;

use Exception;


class BackendController extends Controller
{
    public function obtenerErrores_JSON(Request $request)
    {
        // Definir las reglas a subclasificar
        $categoriasReglas = [
            'calculos' => ['Regla: CAU14','Regla: FAU14'],
            'errorImpuesto' => ['Regla: FAS07'],            
            'valorBruto' => ['Regla: FAU06'],
            'id' => ['Regla: FAK44'],
            'errorReferencia' => ['Regla: NOTREFPLGNS'],
        ];
    
        // Definir las categorías de errores y palabras clave asociadas
        $categorias = [
            'vencimiento' => ['resolucion vencida', 'está vencida', 'error la resolución'],
            'email' => ['email'],
            'tag' => ['no existe TAG_NAME'],
            'calculos' => ['campos mandatorios'],
            'sin_resolucion' => ['sin resolución','error no se encuentra ninguna resolucion'],
            'nombre' => ['nombre', 'error al dato nombre'],
            'folios-fuera-rangos' => ['error folio de factura fuera'],
            'folios-agotados' => ['error los folios de la'],
            'resolucion_nocoincide' => ['error el tipo de resolución no coincide con el tipo de documento'],
            'negativos' => ['valores negativos'],
            'timbrador'=> ['error comunicacion DIAN'],
        ];
    
        // Combinar las claves de ambos arrays (categorias y categoriasReglas)
        $categoriasCombinadas = array_merge($categorias, $categoriasReglas);

        // Inicializar los contadores para cada categoría combinada
        $contadores = array_fill_keys(array_keys($categoriasCombinadas), 0);
        $contadores['otros'] = 0;
        $contadores['error-dian'] = 0;
    
        // Ruta del archivo TXT subido
        $path = $request->file('errorFile')->getRealPath();
    
        // Leer el archivo TXT línea por línea
        $errores = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
        // Procesar cada línea
        foreach ($errores as $linea) {
            $errorData = json_decode($linea, true);
    
            if (json_last_error() !== JSON_ERROR_NONE) {
                // Si la línea no es un JSON válido, se ignora
                continue;
            }
    
            // Verificar errores en el campo "error"
            if (isset($errorData['error']) && !empty($errorData['error'])) {
                $clasificado = false;
                $errorTexto = strtolower($errorData['error']); // Convertir a minúsculas para comparación
    
                foreach ($categorias as $tipo => $palabrasClave) {
                    foreach ($palabrasClave as $palabra) {
                        if (strpos($errorTexto, strtolower($palabra)) !== false) { // Comparar con minúsculas
                            $contadores[$tipo]++;
                            $clasificado = true;
                            break 2; // Salir de ambos loops
                        }
                    }
                }
    
                if (!$clasificado) {
                    $contadores['otros']++;
                }
            }
    
            // Verificar errores en el campo "DIAN"
            if (isset($errorData['DIAN']) && is_array($errorData['DIAN'])) {
                foreach ($errorData['DIAN'] as $dianError) {
                    if (isset($dianError['Descripcion']) && !empty($dianError['Descripcion'])) {
                        $clasificado = false;
                        $descripcion = strtolower($dianError['Descripcion']);
    
                        if (isset($dianError['Respuesta'][0]['descripcion'])) {
                            $respuestaDescripcion = strtolower($dianError['Respuesta'][0]['descripcion']);
                        }
    
                        foreach ($categorias as $tipo => $palabrasClave) {
                            foreach ($palabrasClave as $palabra) {
                                if (strpos($descripcion, strtolower($palabra)) !== false) {
                                    if (strpos($descripcion, 'campos mandatorios') !== false) {
                                        // Subcategorías para campos mandatorios
                                        foreach ($categoriasReglas as $subCategoria => $reglaReglas) {
                                            foreach ($reglaReglas as $regla) {
                                                if (strpos($respuestaDescripcion, strtolower($regla)) !== false) {
                                                    $contadores[$subCategoria]++;
                                                    $clasificado = true;
                                                    break 4; // Salir de ambos loops
                                                }
                                            }
                                        }
                                    } else {
                                        $contadores[$tipo]++;
                                        $clasificado = true;
                                        break 2; // Salir de ambos loops
                                    }
                                }
                            }
                        }
    
                        if (!$clasificado) {
                            $contadores['otros']++;
                        }
                    }
                }
                // caso DIAN[] EMPTY
                if(empty($errorData['DIAN'])){
                    $contadores['error-dian']++;
                }
            }
        }
    
        // Generar una salida con los contadores
        $output = [];
        foreach ($contadores as $tipo => $cantidad) {
            $output[] = "$tipo: $cantidad";
        }
    
        // Generar un archivo TXT con los resultados
        $outputPath = storage_path('app/errores.txt');
        file_put_contents($outputPath, implode(PHP_EOL, $output));
    
        // Descargar el archivo generado
        return response()->download($outputPath, 'errores.txt')->deleteFileAfterSend(true);
    }
    
    public function cargarClientes_xlsxs(Request $request,HelpDeskServiceInterface $helpdeskService,){

        // La contraseña ingresada por el usuario
        $inputPassword = $request->input('adminPassword');

        // Comparar contraseña con la de entorno
        if ($inputPassword != config('app.admin-password')) {
            return response()->json([
                'error' => true,
                'message' => 'Contraseña no válida'
            ], 401); // Código HTTP 401 (No Autorizado)
        }

        set_time_limit(0);
        ini_set("memory_limit", -1);

        try {
            
            // Validar el archivo
            $request->validate([
                'usersFile' => 'required|file|mimes:xlsx,xls', // Máximo 2MB y formatos xlsx/xls
            ]);

            // Ruta del archivo XLSX subido
            // Cargar el archivo
            $path = $request->file('usersFile')->getRealPath();            
            $excel = IOFactory::load($path);
            $hojas = $excel->getSheetNames();
            foreach( $hojas as $nombreHoja ) {
                $hoja = $excel->getSheet($nombreHoja);

                // Número de filas en la hoja
                $filas = $hoja->getHighestRow();

                // Procesar filas si hay más de una (asumiendo que la primera es encabezado)
                if ($filas > 1) {
                    for ($i = 7; $i <= $filas; $i++) { // Comienza en 2 si la fila 1 es encabezado
                        $dataInput[] = [
                            'nombre' => $hoja->getCell("A$i")->getValue(), 
                            'telefono' => $hoja->getCell("B$i")->getValue(), 
                            'direccion' => $hoja->getCell("C$i")->getValue(), 
                            'correo' => $hoja->getCell("D$i")->getValue(), 
                            'razon_social' => $hoja->getCell("F$i")->getValue(), 
                            'nit' => $hoja->getCell("G$i")->getValue(), 
                            'brand_id' => $hoja->getCell("I$i")->getValue(), 
                            'store_id' => $hoja->getCell("J$i")->getValue(), 
                            'anydesk' => $hoja->getCell("K$i")->getValue(), 
                            'contrasena' => $hoja->getCell("L$i")->getValue(), 
                            'resol_1' => $hoja->getCell("N$i")->getValue(), 
                            'prefijo_fact_1' => $hoja->getCell("O$i")->getValue(), 
                            'prefijo_nota_1' => $hoja->getCell("P$i")->getValue(), 
                            'fecha_resol_1' => $hoja->getCell("T$i")->getValue(), 
                            'resol_2' => $hoja->getCell("U$i")->getValue(),
                            'prefijo_fact_2' => $hoja->getCell("V$i")->getValue(), 
                            'prefijo_nota_2' => $hoja->getCell("W$i")->getValue(), 
                            'fecha_resol_2' => $hoja->getCell("AA$i")->getValue(),  
                            'usuario' => $hoja->getCell("AC$i")->getValue(),
                            'ciudad' => $hoja->getCell("AC$i")->getValue(),
                            'departamento'=> $hoja->getCell("AE$i")->getValue(),
                            'codigo_postal'=> $hoja->getCell("AE$i")->getValue(),
                        ];

                        $helpdeskService->createLocation($dataInput, $helpdeskService);
 

                        $queryCreateUser= "INSERT INTO glpi_users (name,password,password_last_update,phone,phone2,mobile,realname,firstname,locations_id,`language`,use_mode,list_limit,is_active,comment,auths_id,authtype,last_login,date_mod,date_sync,is_deleted,profiles_id,entities_id,usertitles_id,usercategories_id,date_format,number_format,names_format,csv_delimiter,is_ids_visible,use_flat_dropdowntree,show_jobs_at_login,priority_1,priority_2,priority_3,priority_4,priority_5,priority_6,followup_private,task_private,default_requesttypes_id,password_forget_token,password_forget_token_date,user_dn,user_dn_hash,registration_number,show_count_on_tabs,refresh_views,set_default_tech,personal_token,personal_token_date,api_token,api_token_date,cookie_token,cookie_token_date,display_count_on_home,notification_to_myself,duedateok_color,duedatewarning_color,duedatecritical_color,duedatewarning_less,duedatecritical_less,duedatewarning_unit,duedatecritical_unit,display_options,is_deleted_ldap,pdffont,picture,begin_date,end_date,keep_devices_when_purging_item,privatebookmarkorder,backcreated,task_state,palette,page_layout,fold_menu,fold_search,savedsearches_pinned,timeline_order,itil_layout,richtext_layout,set_default_requester,lock_autolock_mode,lock_directunlock_notification,date_creation,highcontrast_css,plannings,sync_field,groups_id,users_id_supervisor,timezone,default_dashboard_central,default_dashboard_assets,default_dashboard_helpdesk,default_dashboard_mini_ticket,default_central_tab,nickname,timeline_action_btn_layout,timeline_date_format,use_flat_dropdowntree_on_search_result) 
                        VALUES
                        ('tienda_prueba','$2y$10$7K9vaWiUTyGMbpuvjLvhVOuFADyR8E9bUpWxwFEuFJXjbAzh8Ed8i','2024-12-18 10:05:03','','','3115991435','','',201,NULL,0,NULL,1,'***************************
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
    
                        $Insertar_Cliente  = DB::connection('mysql_second')->select($queryInsert);
                    }
                }
            }
            
        } catch (Exception $e) {
            return $e->getMessage() . "---" . $e->getLine() . "---Error fila: " . $i;
        }

    }
}
