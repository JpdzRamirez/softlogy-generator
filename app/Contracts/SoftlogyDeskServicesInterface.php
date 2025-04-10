<?php

namespace App\Contracts;

interface SoftlogyDeskServicesInterface
{
    
    public function getSessionToken();

    public function reportStatusStore(array $data);
    
}
