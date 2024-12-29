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


        // Validar resultado
        if (isset($authResult['error'])) {
            return response()->json(['error' => $authResult['error']], 400); // Error de autenticación
        }

        // Respuesta exitosa con los datos necesarios
        return response()->json([
            'id' => $authResult->id,
            'name' => $authResult->name,
            'realname' => $authResult->realname,
            'firstname' => $authResult->firstname,
            'mobile' => $authResult->mobile,
            'phone' => $authResult->phone,
            'is_active' => $authResult->is_active,
            'tickets' => $authResult->tickets,
            'picture' => $authResult->picture,
            'version' => 1.0, // Incluye la versión actual
        ], 200);

    }
}
