<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ValidateXMLRequest;


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
        $prefijo=$request->input("prefijo");
        $nitEmisor=$request->input("nitEmisor");
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

    public function refacturarXML(ValidateXMLRequest $request){

            // Ruta del archivo TXT subido
            $path = $request->file('xmlFactura')->getRealPath();

            // Leer el contenido del archivo
            $xmlContent  = file_get_contents($path);

            // Cargar el contenido en un objeto SimpleXML
            try {
                $xml = new SimpleXMLElement($xmlContent);

                $response=$this->xmlService->xmlProcessData($xml, $request);

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
}

?>
