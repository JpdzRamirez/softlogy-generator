<?php

namespace App\Contracts;

interface CastServicesInterface
{
    public function processPhoto(object $photo);
    public function formatDate(string $date,string $format);
    public function undecodeBase64(string $base64);
}