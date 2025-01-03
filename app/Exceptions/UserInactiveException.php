<?php

namespace App\Exceptions;

use Exception;

class UserInactiveException extends Exception
{
    public function __construct($message = "Usuario inactivo", $code = 403)
    {
        parent::__construct($message, $code);
    }
}
