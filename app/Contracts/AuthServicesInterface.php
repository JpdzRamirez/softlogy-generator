<?php

namespace App\Contracts;

interface AuthServicesInterface
{
    public function Authenticate(array $data);

    public function makeBycript(String $text);

    public function makeDecrypt(string $encryptedText);
}