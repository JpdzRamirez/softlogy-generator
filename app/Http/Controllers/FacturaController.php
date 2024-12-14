<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FacturaController extends Controller
{
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
}

?>
