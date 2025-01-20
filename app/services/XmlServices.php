<?php

namespace app\services;


use App\Contracts\XmlServicesInterface;
use App\Models\Paises;

use SimpleXMLElement;
use Exception;

class XmlServices implements XmlServicesInterface
{

    protected $xmlService;

    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function xmlProcessData($xml, $request)
    {

        // Obtenemos todos los input del request en un array asociado al name
        $data = $request->all();

        // Modificar valores existentes
        // Asignar valores a 'tiporeceptor' y 'tipoDocRec'
        if (in_array($data['tipeDocument'], [1, 2, 3])) {
            $xml->Encabezado->tiporeceptor = 1;
        } elseif ($data['tipeDocument'] == 4) {
            $xml->Encabezado->tiporeceptor = 2;
        }

        $tipoDocRecMap = [
            1 => 22,
            2 => 50,
            3 => 31,
            4 => 13,
        ];
        if (isset($data['tipeDocument']) && isset($tipoDocRecMap[$data['tipeDocument']])) {
            $xml->Encabezado->tipoDocRec = $tipoDocRecMap[$data['tipeDocument']];
        }

        // Asignar valores básicos si están definidos
        if (isset($data['identificator'])) {
            $xml->Encabezado->nitreceptor = $data['identificator'];
        }
        if (isset($data['digit'])) {
            $xml->Encabezado->digitoverificacion = $data['digit'];
        }
        if (isset($data['firstName'])) {
            $xml->Encabezado->nombrereceptor = $data['firstName'];
        }
        if (isset($data['postalCode'])) {
            $xml->Encabezado->codigopostal = $data['postalCode'];
        }

        $xml->Encabezado->paisreceptor = $data['country'] ?? 'CO';

        // Obtener información del país
        $pais = Paises::where('codigo', $data['country'])->first();

        // Asignar estado, ciudad, y dirección con valores predeterminados
        if (isset($data['state'])) {
            $xml->Encabezado->departamentoreceptor = $data['state'];
        } elseif ($pais) {
            $xml->Encabezado->departamentoreceptor = $pais->nombre;
        }

        if (isset($data['city'])) {
            $xml->Encabezado->ciudadreceptor = $data['city'];
        } elseif ($pais) {
            $xml->Encabezado->ciudadreceptor = $pais->nombre;
        }

        if (isset($data['address'])) {
            $xml->Encabezado->direccionreceptor = $data['address'];
        } elseif ($pais) {
            $xml->Encabezado->direccionreceptor = $pais->nombre;
        }

        // Asignar correo electrónico si está presente
        if (isset($data['emailReceptor'])) {
            $xml->Encabezado->mailreceptor = $data['emailReceptor'];
            $xml->Encabezado->mailreceptorcontacto = $data['emailReceptor'];
        }

        // Asignar número de teléfono si está presente
        if (isset($data['phone'])) {
            $xml->Encabezado->telefonoreceptor = $data['phone'];
        }

        // Asignar nombre comercial y contacto
        if (in_array($data['tipeDocument'], [2, 3]) && isset($data['firstName'])) {
            $xml->Encabezado->nombrecomercialreceptor = $data['firstName'];
            $xml->Encabezado->nombrecontactoreceptor = $data['firstName'];
        } else {
            if (isset($data['firstName'], $data['secondName'], $data['lastName'])) {
                $nombreCompleto = "{$data['firstName']} {$data['secondName']} {$data['lastName']}";
                $xml->Encabezado->nombrecomercialreceptor = $nombreCompleto;
                $xml->Encabezado->nombrecontactoreceptor = $nombreCompleto;
            } else {
                // Asignar solo nombre si alguno de los campos está presente
                if (isset($data['firstName'])) {
                    $xml->Encabezado->nombrecontactoreceptor = $data['firstName'];
                }
                if (isset($data['secondName'])) {
                    $xml->Encabezado->segnombrereceptor = $data['secondName'];
                }
                if (isset($data['lastName'])) {
                    $xml->Encabezado->apellidosreceptor = $data['lastName'];
                }
            }
        }

        // Asignar folio y nciddoc si están presentes
        if (isset($data['folio'])) {
            $xml->Encabezado->folio = $data['folio'];
            $xml->Encabezado->nciddoc = $xml->Encabezado->prefijo . $data['folio'];
        }

        // Asignar fecha y hora
        $fecha = now()->format('Y-m-d');
        $hora = now()->subHour()->format('H:i:s');
        $xml->Encabezado->fecha = $fecha;
        $xml->Encabezado->fechavencimiento = $fecha;
        $xml->Encabezado->hora = $hora;

        return $xml;
    }
    public function xmlCambiarFolio($rowText, $nuevoFolio)
    {
        try {
            // Limpiar la cadena de entrada
            $rowText = str_replace(';NULL;', '', $rowText); // Eliminar ;NULL;
            $rowText = str_replace(';', '', $rowText);
            // armar el xml
            $rowText = '<?xml version="1.0" encoding="UTF-8"?>' . $rowText;

            $xml = simplexml_load_string($rowText);
            // Cambiamos el folio
            $xml->Encabezado->folio = $nuevoFolio;
            $xml->Encabezado->nciddoc = $xml->Encabezado->prefijo.$nuevoFolio;
            $fecha = now()->format('Y-m-d');
            $hora = now()->subHour()->format('H:i:s');
            $xml->Encabezado->fecha = $fecha;
            $xml->Encabezado->fechavencimiento = $fecha;
            $xml->Encabezado->hora = $hora;
            // Retornar el XML como texto
            return $xml->asXML();
        } catch (Exception $e) {
            throw $e;
        }
    }
}
