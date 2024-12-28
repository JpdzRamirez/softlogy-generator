<?php

namespace App\Http\Controllers;

use App\Contracts\AuthServicesInterface;
use App\Http\Requests\ValidateLoginRequest;

use Illuminate\Http\Request;

class PuntosVentaController extends Controller
{
    public function loginOAuth(ValidateLoginRequest $request, AuthServicesInterface $auth)
    {
                
        // Se valida los datos con inyección de dependencias de un validador 
        $validatedData = $request->only('username', 'password');

        // Puedes proceder con la lógica de autenticación
        $authResult = $auth->authenticate($validatedData);


        if (!$authResult) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }


    }
}
