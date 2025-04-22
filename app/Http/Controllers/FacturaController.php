<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ValidateXMLRequest;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use App\Contracts\XmlServicesInterface;
use App\Contracts\CastServicesInterface;

use SimpleXMLElement;
use Exception;

class FacturaController extends Controller
{

    protected $xmlService;
    protected $castService;

    // Inyección de dependencias
    public function __construct(XmlServicesInterface $xmlService,CastServicesInterface $castService )
    {
        $this->xmlService = $xmlService; // Aquí se inyecta la implementación de la interfaz
        $this->castService = $castService;
    }

    public function obtenerCUFES_Folios(Request $request)
    {
        $prefijo = $request->input("prefijo");
        $nitEmisor = $request->input("nitEmisor");
        // Ruta del archivo TXT subido
        $path = $request->file('archivo')->getRealPath();

        // Leer el archivo TXT línea por línea
        $folios = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        // Vector para almacenar los CUFEs
        $cufes = [];

        // URL de la API
        $url = "https://timbrador.ls-da.com/v2/factura/obtener";

        // Autorización (reemplaza con tu valor)
        $authorization = env('AUTHORIZATION_TOKEN');

        foreach ($folios as $folio) {
            // Construir el JSON a enviar
            $payload = [
                "prefijo" => $prefijo,
                "folio" => $folio,
                "numeroEmisor" => $nitEmisor,
                "numeroReceptor" => "222222222222"
            ];

            // Realizar la petición cURL
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Authorization: $authorization",
                "Content-Type: application/json",
                "Content-Length: " . strlen(json_encode($payload))
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_VERBOSE, true); // Habilitar depuración
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

            $response = curl_exec($ch);
            curl_close($ch);

            if ($response === false) {
                continue; // Saltar si hubo un error
            }

            $responseData = json_decode($response, true);

            // Extraer el CUFE si está presente
            if (isset($responseData['cufe'])) {
                $cufes[] = $responseData['cufe'];
            }

            // Pausa para no ser bloqueado
            sleep(5);
        }

        // Generar un archivo TXT con los CUFEs
        $outputPath = storage_path('app/cufes.txt');
        file_put_contents($outputPath, implode(PHP_EOL, $cufes));

        // Descargar el archivo generado
        return response()->download($outputPath, 'cufes.txt')->deleteFileAfterSend(true);
    }

    public function refacturarXML(ValidateXMLRequest $request)
    {

        // Ruta del archivo TXT subido
        $path = $request->file('xmlFactura')->getRealPath();

        // Leer el contenido del archivo
        $xmlContent  = file_get_contents($path);

        // Cargar el contenido en un objeto SimpleXML
        try {
            $xml = new SimpleXMLElement($xmlContent);

            $response = $this->xmlService->xmlProcessData($xml, $request);

            // Ruta donde se guardará el archivo
            $filePath = public_path('storage/documents/xml_modificado.xml');

            $response = $this->xmlService->xmlProcessData($xml, $request);
            $response->asXML($filePath);

            // Configurar las cabeceras para la descarga del archivo
            // El archivo se elimina después de enviarlo
            return response()->download($filePath, 'xml_modificado.xml')->deleteFileAfterSend(true);
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function remplazarDatos(Request $request)
    {
        // Validar el archivo
        $request->validate([
            'foliosPisados' => 'required|file|mimes:xlsx,xls',
            'documentOption' => 'nullable|string|in:base64,contingency'
        ]);
    
        //$nuevoFolio = $request['inicio'];
        $filasNoProcesadas = []; // Inicializamos el array para las filas no procesadas.
        $responses = []; // Almacena los responses como cadenas.
        $fileName = 'responses.txt'; // Nombre del archivo generado.
        $filePath = storage_path("app/public/{$fileName}"); // Ruta completa del archivo.
    
        try {
            // Cargar el archivo y leer la hoja principal
            $path = $request->file('foliosPisados')->getRealPath();
            $excel = IOFactory::load($path);
            //solo leemos la hoja 1
            $hoja = $excel->getSheet(0);
            $filas = $hoja->getHighestRow(); // Obtenemos el total de filas
    
            // Verificar y procesar las filas
            if ($filas > 1) {
                for ($i = 2; $i <= $filas; $i++) {
                    // Celdas donde tomamos los nuevos datos para el xml
                    if($request->input('documentOption') === 'base64'){
                        $facturaDecoded=$this->castService->undecodeBase64($hoja->getCell("A$i")->getValue());                        
                        $dataInput = [
                            'factura' => $facturaDecoded,
                            'folio'=>$hoja->getCell("B$i")->getValue() ?: '',
                            'type' => 1
                        ];  
                    }elseif($request->input('documentOption') === 'contingency'){
                        $facturaDecoded=$this->castService->undecodeBase64($hoja->getCell("A$i")->getValue());

                        $cellValue = $hoja->getCell("E$i")->getValue();
                        $fecha = Date::isDateTime($hoja->getCell("E$i")) 
                            ? Date::excelToDateTimeObject($cellValue)->format('Y-m-d') 
                            : $cellValue;
                        $dataInput = [
                            'factura' => $facturaDecoded,
                            'prefijo'=>$hoja->getCell("B$i")->getValue() ?: '',
                            'folio'=>$hoja->getCell("C$i")->getValue() ?: '',
                            'resolucion'=>$hoja->getCell("D$i")->getValue() ?: '',
                            'fecha' => $fecha ?: '',
                            'tipoComprobante' => $hoja->getCell("F$i")->getValue() ?: '',
                            'xslt'=> $hoja->getCell("G$i")->getValue() ?: '',
                            'type' => 3
                        ];  
                    } else{
                        $dataInput = [
                            'factura' => $hoja->getCell("A$i")->getValue() ?: '',
                            'prefijo'=>$hoja->getCell("B$i")->getValue() ?: '',
                            'folio'=>$hoja->getCell("C$i")->getValue() ?: '',
                            'resolucion'=>$hoja->getCell("D$i")->getValue() ?: '',
                            'type' => 2
                        ];   
                    }             
                    if (empty($dataInput['factura'])) {
                        // Si la celda está vacía, la agregamos a las filas no procesadas
                        $filasNoProcesadas[] = $i;
                    } else {
                        // Convertir la celda a XML y procesar                        
                        $response = $this->xmlService->xmlCambiarDatos($dataInput);
                        $responses[] = $response; // Agregar el response al array con el número de fila                        
                    }
                }
    
                // Crear el archivo TXT con los responses
                file_put_contents($filePath, implode('', $responses));
            }
    
            // Redirigir con mensajes en la sesión
            return redirect()->route('back.tools')->with([
                'response_process' => $filasNoProcesadas,
                'mensage_session' => 'El archivo ha sido procesado correctamente.',
                'response_file_ok' => $fileName,
            ]);
    
        } catch (Exception $e) {
            // En caso de error, redirigir con mensaje de error
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function generarXMLS(Request $request)
    {
                // Validar el archivo
                $request->validate([
                    'listadoFolios' => 'required|file|mimes:xlsx,xls',
                    'brandIdSelect' => 'required|integer',
                ]);
            
                $filasNoProcesadas = []; // Inicializamos el array para las filas no procesadas.
                $responses = []; // Almacena los responses como cadenas.
                $fileName = 'responses.txt'; // Nombre del archivo generado.
                $filePath = storage_path("app/public/{$fileName}"); // Ruta completa del archivo.
            
                try {
                    // Cargar el archivo y leer la hoja principal
                    $path = $request->file('listadoFolios')->getRealPath();
                    $excel = IOFactory::load($path);
                    $hoja = $excel->getSheet(0);
                    $filas = $hoja->getHighestRow(); // Obtenemos el total de filas
            
                    // Verificar y procesar las filas
                    if ($filas > 1) {
                        for ($i = 2; $i <= $filas; $i++) {
            
                            $dataInput = [
                                'prefijo' => $hoja->getCell("M$i")->getValue() ?: '',
                                'folio' => $hoja->getCell("N$i")->getValue() ?: '',
                                'noresolucion' => $hoja->getCell("L$i")->getValue() ?: '',
                                'folioPOS' => $hoja->getCell("AB$i")->getValue() ?: '',
                                'checkid' => $hoja->getCell("AS$i")->getValue() ?: '',
                                'BrandId' => $hoja->getCell("C$i")->getValue() ?: '',
                                'StoreId' => $hoja->getCell("D$i")->getValue() ?: '',
                            ];
            
                            if (empty($dataInput['prefijo'])||empty($dataInput['folio'])) {
                                // Si la celda está vacía, la agregamos a las filas no procesadas
                                $filasNoProcesadas[] = $i;
                            } else {
                                // Convertir la celda a XML y procesar                        
                                $response = $this->xmlService->xmlGenerar($dataInput);
                                $responses[] = $response; // Agregar el response al array con el número de fila
                            }
                        }
            
                        // Crear el archivo TXT con los responses
                        file_put_contents($filePath, implode('', $responses));
                    }
                    // Redirigir con mensajes en la sesión
                    return redirect()->route('back.tools')->with([
                        'response_file_failed' => $filasNoProcesadas,
                        'mensage_session' => 'El archivo ha sido procesado correctamente.',
                        'response_file_ok' => $fileName,
                    ]);
                }catch(Exception $e){
                    // En caso de error, redirigir con mensaje de error
                    return redirect()->back()->withErrors(['error' => $e->getMessage()]);
                }
    }

    public function generarXMLSDescuentos(Request $request)
    {
                // Validar el archivo
                $request->validate([
                    'listadoXML' => 'required|file|mimes:xlsx,xls',                    
                ]);
            
                $filasNoProcesadas = []; // Inicializamos el array para las filas no procesadas.
                $responsesOk = []; // XML generados correctamente.
                $responsesFailed = []; // Facturas no procesadas.
                // Definir nombres y rutas de los archivos
                $fileOk = 'responses_ok.txt';
                $fileFailed = 'responses_failed.txt';
                $filePathOk = storage_path("app/public/{$fileOk}");
                $filePathFailed = storage_path("app/public/{$fileFailed}");
            
                try {
                    // Cargar el archivo y leer la hoja principal
                    $path = $request->file('listadoXML')->getRealPath();
                    $excel = IOFactory::load($path);
                    $hoja = $excel->getSheet(0);
                    $filas = $hoja->getHighestRow(); // Obtenemos el total de filas
            
                    // Verificar y procesar las filas
                    if ($filas >= 1) {
                        for ($i = 2; $i <= $filas; $i++) {
                            $factura = $hoja->getCell("A$i")->getValue() ?: '';
            
                            if (empty($factura)) {
                                // Si la celda está vacía, agregamos la línea a "fallidos"
                                $responsesFailed[] = "Línea {$i}: Factura vacía o incompleta";
                            } else {
                                try {
                                    // Convertir la celda a XML y procesar                        
                                    $response = $this->xmlService->xmlAplicarDescuentos($factura);
                                    if($response['status']===true){
                                        $responsesOk[] = $response['xml'];
                                    }else{
                                        $responsesFailed[] =  $response['xml'];
                                    }
                                    
                                } catch (Exception $e) {
                                    // Si hay un error en el proceso de XML, lo registramos en fallidos
                                    throw $e;
                                }
                            }
                        // Crear el archivo TXT con los responses
                        // Guardar los archivos de respuestas
                        file_put_contents($filePathOk, implode("", $responsesOk));
                        file_put_contents($filePathFailed, implode("", $responsesFailed));
                    }
                }
                    // Redirigir con mensajes en la sesión
                    return redirect()->route('back.tools')->with([
                        'response_process' => $filasNoProcesadas,
                        'mensage_session' => 'El archivo ha sido procesado correctamente.',
                        'response_file_ok' => $fileOk,
                        'response_file_failed' => $fileFailed,
                    ]);
                }catch(Exception $e){
                    // En caso de error, redirigir con mensaje de error
                    return redirect()->back()->withErrors(['error' => $e->getMessage()]);
                }
    }
    
}
