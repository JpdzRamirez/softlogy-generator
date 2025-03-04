<?php

namespace App\Contracts;

interface XmlServicesInterface
{
    public function xmlProcessData($xml, $request);
    
    public function xmlCambiarDatos(array $xml);

    public function xmlGenerar($xmlData);
    
}