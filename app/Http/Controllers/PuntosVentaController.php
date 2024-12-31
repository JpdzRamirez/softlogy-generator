<?php

namespace App\Http\Controllers;

use App\Contracts\AuthServicesInterface;
use App\Contracts\HelpDeskServiceInterface;
use App\Http\Requests\ValidateLoginRequest;


class PuntosVentaController extends Controller
{
    public function loginOAuth(ValidateLoginRequest $request, AuthServicesInterface $auth,HelpDeskServiceInterface $helpServices )
    {
                
        // Se valida los datos con inyección de dependencias de un validador 
        $validatedData = $request->only('username', 'password');

        // Puedes proceder con la lógica de autenticación
        $authResult = $auth->authenticate($validatedData);


        // Validar resultado
        if (isset($authResult['error'])) {
            return response()->json(['error' => $authResult['error']], 400); // Error de autenticación
        }


        // Obtenemos el arreglo de numero de tickets abierto, en curso, cerrado del usuario autenticado
        $ticketsCount= $helpServices->getTicketsCount($authResult->id);

        // Respuesta exitosa con los datos necesarios
        return response()->json([
            'id' => $authResult['user']->id,
            'name' => $authResult['user']->name,
            'realname' => $authResult['user']->realname,
            'firstname' => $authResult['user']->firstname,
            'mobile' => $authResult['user']->mobile,
            'phone' => $authResult['user']->phone,
            'is_active' => $authResult['user']->is_active,
            'tickets' => $ticketsCount,
            'picture' => $authResult['user']->picture,
            'token' => $authResult['token'],
            'version' => 1.0, // Incluye la versión actual
        ], 200);

    }
}
