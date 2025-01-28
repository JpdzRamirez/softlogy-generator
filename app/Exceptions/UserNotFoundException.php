<?php

namespace App\Exceptions;

use Exception;

class UserNotFoundException extends Exception
{
    // Puedes personalizar el constructor si lo deseas
    public function __construct($message = "Usuario no existe en nuestros registros", $code = 404)
    {
        parent::__construct($message, $code);
    }
}