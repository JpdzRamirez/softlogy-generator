<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Contracts\HelpDeskServicesInterface;

use PhpOffice\PhpSpreadsheet\IOFactory;

use Exception;


class BackendController extends Controller
{
    public function obtenerErrores_JSON(Request $request)
    {
        // Definir las reglas a subclasificar
        $categoriasReglas = [
            'calculos' => ['Regla: CAU14', 'Regla: FAU14', 'Regla: CAU04', 'Regla: FAW03', 'Regla: CAU08', 'Regla: FBE08', 'Regla: FAU08'],
            'errorImpuesto' => ['Regla: FAS07', 'Regla: CAU02', 'Regla: FAU02', 'Regla: FAX07', 'Regla: FAX05'],
            'errorCamposReceptor' => ['Regla: FAJ44b'],
            'duplicidadDocumento' => ['Regla: 90'],
            'valorBruto' => ['Regla: FAU06'],
            'errorXML' => ['Regla: FAX11', 'Regla: FAX09'],
            'id' => ['Regla: FAK44'],
            'errorComunicacion' => ['Regla: ZE02', 'Regla: FAB27b', 'Regla: FAD09e', 'Regla: FAJ26'],
            'errorReferencia' => ['Regla: NOTREFPLGNS'],
        ];

        // Definir las categorías de errores y palabras clave asociadas
        $categorias = [
            'resolucion_vencida' => ['resolucion vencida', 'está vencida', 'error la resolución'],
            'tag' => ['no existe TAG_NAME'],
            'datosFactura' => ['el dato Tipo Impuesto', 'al dato Nombre Receptor es obligatorio', 'el dato Mail Receptor es obligatorio'],
            'calculos' => ['campos mandatorios'],
            'sin_resolucion' => ['sin resolución', 'error no se encuentra ninguna resolucion'],
            'nombre' => ['nombre', 'error al dato nombre'],
            'folios-fuera-rangos' => ['error folio de factura fuera'],
            'folios-agotados' => ['error los folios de la'],
            'resolucion_nocoincide' => ['error el tipo de resolución no coincide con el tipo de documento'],
            'negativos' => ['valores negativos'],
            'timbrador' => ['error comunicacion DIAN'],
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
                if (empty($errorData['DIAN'])) {
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
    public function descargarFormato_xlsx(Request $request)
    {
        // Obtener el tipo de formato desde la ruta
        $tipoFormato = $request->route('tipo');

        // Definir la ruta del archivo según el tipo solicitado
        $formatos = [
            'cargue-helpdesk' => 'documents/Formato_Cargue_v1.xlsx',
            'cargue-facturas' => 'documents/Formato_Cargue_Facturas.xlsx',
            'crear-contingencias'=> 'documents/Formato_Crear_Contingencias.xlsx',
            'crear-notas'=> 'documents/Formato_Cargue_Notas_Credito.xlsx',
            'json-detalles'=> 'documents/Formato_Cargue_JSON_Detalles.xlsx',
        ];

        // Verificar si el formato solicitado existe en la lista
        if (!array_key_exists($tipoFormato, $formatos)) {
            return back()->withErrors(['error' => 'Tipo de formato no válido.']);
        }

        // Obtener la ruta completa del archivo
        $filePath = storage_path($formatos[$tipoFormato]);

        // Verificar si el archivo existe antes de intentar descargarlo
        if (!file_exists($filePath)) {
            return response()->json(['error' => 'El archivo no existe.'], 404);
        }

        // Retorna el archivo para su descarga con un nombre adecuado
        return response()->download($filePath, basename($filePath));
    }

    public function cargarClientes_xlsx(Request $request, HelpDeskServicesInterface $helpdeskService,)
    {

        // La contraseña ingresada por el usuario
        $inputPassword = $request->input('adminPassword');

        // Comparar contraseña con la de entorno
        if ($inputPassword != config('app.admin-password')) {
            return response()->json([
                'error' => true,
                'title' => 'Error de Credenciales, Status: 401',
                'message' => 'Contraseña no válida'
            ], 401); // Código HTTP 401 (No Autorizado)
        }


        try {

            // Validar el archivo
            $request->validate([
                'usersFile' => 'required|file|mimes:xlsx,xls', // Máximo 2MB y formatos xlsx/xls
            ]);

            // Ruta del archivo XLSX subido
            // Cargar el archivo
            $path = $request->file('usersFile')->getRealPath();
            $excel = IOFactory::load($path);

            // Obtenemos la hoja principal para el cargue
            $hoja = $excel->getSheet(0);

            // Número de filas en la hoja
            $filas = $hoja->getHighestRow();
            $filasNoProcesadas = [];
            $filasProcesadas = 0;
            // Procesar filas si hay más de una (asumiendo que la primera es encabezado)
            if ($filas > 1) {
                for ($i = 7; $i <= $filas; $i++) { // Comienza en 2 si la fila 1 es encabezado
                    $isvalid = $hoja->getCell("A$i")->getValue() ?: false;
                    if (!$isvalid) {
                        continue;
                    }
                    $dataInput = [
                        'nombre' => $hoja->getCell("A$i")->getValue() ?: '',
                        'telefono' => $hoja->getCell("B$i")->getValue() ?: '',
                        'direccion' => $hoja->getCell("C$i")->getValue() ?: '',
                        'correo' => $hoja->getCell("D$i")->getValue() ?: '',
                        'razon_social' => $hoja->getCell("F$i")->getValue() ?: '',
                        'nit' => $hoja->getCell("G$i")->getValue() ?: '',
                        'brand_id' => $hoja->getCell("I$i")->getValue() ?: '',
                        'store_id' => $hoja->getCell("J$i")->getValue() ?: '',
                        'anydesk' => $hoja->getCell("K$i")->getValue() ?: '',
                        'contrasena' => $hoja->getCell("L$i")->getValue() ?: '',
                        'resol_1' => $hoja->getCell("N$i")->getValue() ?: '',
                        'prefijo_fact_1' => $hoja->getCell("O$i")->getValue() ?: '',
                        'prefijo_nota_1' => $hoja->getCell("P$i")->getValue() ?: '',
                        'fecha_resol_1' => $hoja->getCell("T$i")->getValue() ?: '',
                        'resol_2' => $hoja->getCell("U$i")->getValue() ?: '',
                        'prefijo_fact_2' => $hoja->getCell("V$i")->getValue() ?: '',
                        'prefijo_nota_2' => $hoja->getCell("W$i")->getValue() ?: '',
                        'fecha_resol_2' => $hoja->getCell("AA$i")->getValue() ?: '',
                        'usuario' => $hoja->getCell("AC$i")->getCalculatedValue() ?: '',
                        'ciudad' => $hoja->getCell("AD$i")->getCalculatedValue() ?: '',
                        'departamento' => $hoja->getCell("AE$i")->getCalculatedValue() ?: '',
                        'codigo_postal' => $hoja->getCell("AF$i")->getCalculatedValue() ?: '',
                        'clave_tecnica' => $hoja->getCell("AB$i")->getCalculatedValue() ?: '',
                    ];
                    // Creamos la Ubicación del Punto
                    $locationId = $helpdeskService->createLocation($dataInput);
                    // Cargamos en el arreglo para crear la tienda
                    $dataInput['location_id'] = $locationId ?: 201;

                    $response = $helpdeskService->createEntiti($dataInput);

                    // Si la respuesta no es exitosa, anotamos la fila como no procesada
                    if (!$response) {
                        $filasNoProcesadas[] = $i;
                    } else {
                        $filasProcesadas++; // Incrementamos el contador si la fila fue procesada correctamente
                    }
                }
                $notProcessedRows = count($filasNoProcesadas) > 0 ? implode(', ', $filasNoProcesadas) : 'Ninguna fila fue ignorada';
                // Ending request 
                return response()->json([
                    'status' => true,
                    'records' => $filasProcesadas,
                    'notProcess' => $notProcessedRows,
                    'title' => 'Usuarios Creados Exitosamente'
                ], 200); // Código HTTP 401 (No Autorizado)
            }
        } catch (Exception $e) {
            return response()->json([
                'error' => true,
                'title' => 'Error en HelpDesk Services, Status: 500',
                'message' => "Algo salió mal al crear los puntos de venta. ERROR: {$e->getMessage()}, Archivo: {$e->getFile()}, Línea: {$e->getLine()}."
            ], 500); // Código HTTP 401 (No Autorizado)            
        }
    }
    public function descargarFoliosPisados($filename)
    {
        $path = storage_path("app/public/{$filename}");

        if (file_exists($path)) {
            return response()->download($path);
        }

        abort(404, "El archivo no existe.");
    }
}
