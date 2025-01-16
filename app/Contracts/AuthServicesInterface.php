<?php

namespace App\Contracts;

interface AuthServicesInterface
{
    public function Authenticate(array $data);
}