<?php

namespace App\Contracts;

interface XmlServicesInterface
{
    public function xmlProcessData($xml, $request);
    
    public function xmlCambiarDatos(array $xml);

    public function xmlGenerar($xmlData);
    public function JSONToXML($jsonData);
    public function xmlAplicarDescuentos(String $factura);
    public function xmlCrearNotaCredito(array $factura);
    public function xmlCrearContingencias(array $factura);
    
}