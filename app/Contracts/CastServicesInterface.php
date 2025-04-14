<?php

namespace App\Contracts;

interface CastServicesInterface
{
    public function processPhoto(object $photo);
    public function formatDate(string $date,string $format);
    public function undecodeBase64(string $base64);
    public function jsonNetFormatter(string $date): string|null;
    public function decodeUnicode($string);
}