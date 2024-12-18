<?php

namespace App\Contracts;

interface HelpDeskServiceInterface
{
    public function createLocation(array $location);
    public function createEntiti(array $data);
    
}