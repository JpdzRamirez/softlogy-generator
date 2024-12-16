<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
        $path = $request->file('archivo')->getRealPath();
    
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
    
    public function cargarClientes_xlsxs(Request $request){

        set_time_limit(0);
        ini_set("memory_limit", -1);
        try {
            // Cargar el archivo
            // Ruta del archivo XLSX subido
            $path = $request->file('archivo')->getRealPath();
            $excel = IOFactory::load($path);
            $hojas = $excel->getSheetNames();
            foreach( $hojas as $nombreHoja ) {
                $hoja = $excel->getSheet($nombreHoja);

                // Número de filas en la hoja
                $filas = $hoja->getHighestRow();

                // Procesar filas si hay más de una (asumiendo que la primera es encabezado)
                if ($filas > 1) {
                    for ($i = 2; $i <= $filas; $i++) { // Comienza en 2 si la fila 1 es encabezado
                        $resultados[] = [
                            'columna_1' => $hoja->getCell("A$i")->getValue(), // Columna A
                            'columna_2' => $hoja->getCell("B$i")->getValue(), // Columna B
                            'columna_3' => $hoja->getCell("C$i")->getValue(), // Columna C
                        ];
                    }
                }
            }
            
        } catch (Exception $e) {
            return $ex->getMessage() . "---" . $ex->getLine() . "---Error fila: " . $i;
        }

    }
}
