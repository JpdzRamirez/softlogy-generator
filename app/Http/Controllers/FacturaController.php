<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ValidateXMLRequest;
use PhpOffice\PhpSpreadsheet\IOFactory;

use App\Contracts\XmlServicesInterface;

use SimpleXMLElement;
use Exception;

class FacturaController extends Controller
{

    protected $xmlService;

    // Inyección de dependencias
    public function __construct(XmlServicesInterface $xmlService)
    {
        $this->xmlService = $xmlService; // Aquí se inyecta la implementación de la interfaz
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

    public function remplazarFolios(Request $request)
    {
        // Validar el archivo
        $request->validate([
            'foliosPisados' => 'required|file|mimes:xlsx,xls',
            'inicio' => 'required|integer',
            'fin' => 'required|integer',
        ]);
        
        $nuevoFolio=$request['inicio'];
        $filasNoProcesadas = []; // Inicializamos el array para las filas no procesadas.
        $filePath = null; // Inicializamos la ruta del archivo procesado.
    
        try {
            // Cargar el archivo y leer la hoja principal
            $path = $request->file('foliosPisados')->getRealPath();
            $excel = IOFactory::load($path);
            $hoja = $excel->getSheet(0);
            $filas = $hoja->getHighestRow(); // Obtenemos el total de filas
    
            // Verificar y procesar las filas
            if ($filas > 1) {
                for ($i = 1; $i <= $filas; $i++) { // Comienza en la fila 7

                    if ($nuevoFolio > $request['fin']) {
                        break; // Detenemos el proceso si alcanzamos el fin del rango
                    }

                    $dataInput = [
                        'factura' => $hoja->getCell("A$i")->getValue() ?: '',
                    ];
    
                    if (empty($dataInput['factura'])) {
                        // Si la celda está vacía, la agregamos a las filas no procesadas
                        $filasNoProcesadas[] = $i;
                    } else {
                        // Convertir la celda a XML y procesar                        
                        $response = $this->xmlService->xmlCambiarFolio($dataInput['factura'], $nuevoFolio);
                        $hoja->setCellValue("B$i", $response); // Ejemplo de guardar cambios en columna B                        
                        $nuevoFolio++;
                    }
                }
    
                // Guardar el Excel procesado en una ubicación temporal
                $filePath = storage_path('app/public/Formato_Cargue_Procesado.xlsx');
                $writer = IOFactory::createWriter($excel, 'Xlsx');
                $writer->save($filePath);
            }
    
            // Redirigir con mensajes en la sesión
            return redirect()->route('back.tools')->with([
                'response_process' => $filasNoProcesadas,
                'mensage_session' => 'El archivo ha sido procesado correctamente.',
                'response_file' => 'Formato_Cargue_Procesado.xlsx',
            ]);
        } catch (Exception $e) {
            // En caso de error, redirigir con mensaje de error
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        } finally {
            // Eliminar el archivo temporal si existe
            if ($filePath && file_exists($filePath)) {
                unlink($filePath);
            }
        }
    }
}
