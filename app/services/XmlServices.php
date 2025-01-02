<?php

namespace app\services;


use App\Contracts\XmlServicesInterface;
use App\Models\Paises;

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

    public function xmlProcessData($xml, $request){

        // Obtenemso todos los input del request en un array asociado al name
        $data = $request->all();

        // Modificar valores existentes
            // Asignar el valor de 'tiporeceptor'
        if (in_array($data['tipeDocument'], [1, 2, 3])) {
            $xml->Encabezado->tiporeceptor = 1;
        } elseif ($data['tipeDocument'] == 4) {
            $xml->Encabezado->tiporeceptor = 2;
        }

        // Asignar el valor de 'tipoDocRec'
        switch ($data['tipeDocument']) {
            case 1:
                $xml->Encabezado->tipoDocRec = 22;
                break;
            case 2:
                $xml->Encabezado->tipoDocRec = 50;
                break;
            case 3:
                $xml->Encabezado->tipoDocRec = 31;
                break;
            case 4:
                $xml->Encabezado->tipoDocRec = 13;
                break;
            default:
                // Opción por defecto si no es ninguno de los casos anteriores
                $xml->Encabezado->tipoDocRec = null; // O algún otro valor
                break;
        }

        $xml->Encabezado->nitreceptor = $data['identificator'];

        if(isset($data['digit'])){
            $xml->Encabezado->digitoverificacion = $data['digit']; // Cambiar el valor de digitoverificacion
        }else{
            $xml->Encabezado->digitoverificacion = "0";
        }
        
        $xml->Encabezado->nombrereceptor = $data['firstName']; 

        if(in_array($data['tipeDocument'], [ 2, 3])){
            $xml->Encabezado->nombrecomercialreceptor = $data['firstName'];
        }else{
            $xml->Encabezado->segnombrereceptor = $data['secondName']; 
            $xml->Encabezado->apellidosreceptor = $data['lastName']; 
            $xml->Encabezado->nombrecomercialreceptor = $data['firstName']." ".$data['secondName']." ".$data['lastName']; 
        }
        if(isset($data['postalCode'])){
            $xml->Encabezado->codigopostal = $data['postalCode'];
        }else{
            $xml->Encabezado->codigopostal = "000000";
        }
        $xml->Encabezado->paisreceptor = $data['country'];

        $pais= Paises::where('codigo', $data['country'])->first();

        $xml->Encabezado->codigodepartamento = "000000"; 

        if(isset($data['state'])){            
            $xml->Encabezado->departamentoreceptor = $data['state']; 
        }else{            
            $xml->Encabezado->departamentoreceptor = $pais->nombre;
        }

        $xml->Encabezado->codigociudadreceptor = "000000";

        if(isset($data['city'])){
            $xml->Encabezado->ciudadreceptor = $data['city']; 
        }else{            
            $xml->Encabezado->ciudadreceptor = $pais->nombre;
        }

        if(isset($data['address'])){     
            $xml->Encabezado->direccionreceptor = $data['address'];
        }else{
            $xml->Encabezado->direccionreceptor = $pais->nombre;
        }

        $xml->Encabezado->mailreceptor = $data['emailReceptor'];

        $xml->Encabezado->mailreceptorcontacto = $data['emailReceptor'];

        if(isset($data['phone'])){
            $xml->Encabezado->telefonoreceptor = $data['emailReceptor'];
        }else{
            $xml->Encabezado->telefonoreceptor = "000000";
        }

        if(in_array($data['tipeDocument'], [ 2, 3])){
            $xml->Encabezado->nombrecontactoreceptor = $data['firstName'];
        }else{
            $xml->Encabezado->nombrecontactoreceptor = $data['firstName']." ".$data['secondName']." ".$data['lastName']; 
        }
        
        $xml->Encabezado->folio = $data['folio'];

        $fecha = now()->format('Y-m-d'); // 2024-12-31
        $xml->Encabezado->fecha = $fecha;
        $xml->Encabezado->fechavencimiento = $fecha;
        // Se obtiene la hora actual menos una hora
        $hora = now()->subHour()->format('H:i:s'); 
        $xml->Encabezado->hora = $hora;

        
        $xml->Encabezado->nciddoc = $xml->Encabezado->prefijo.$data['folio'];

        return $xml;
    }

}