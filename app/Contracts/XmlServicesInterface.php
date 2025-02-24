<?php

namespace App\Contracts;

interface XmlServicesInterface
{
    public function xmlProcessData($xml, $request);
    
    public function xmlCambiarFolio($xml, $request);

    public function xmlGenerar($xmlData);
    
}