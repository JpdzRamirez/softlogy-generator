<?php

namespace App\Contracts;

interface AuthServicesInterface
{
    public function Authenticate(array $data);

    public function makeBycript(String $name);

    public function makeDecrypt(string $encryptedText);
}