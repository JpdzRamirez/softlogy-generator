<?php

namespace App\Http\Controllers;

use App\Contracts\AuthServicesInterface;
use App\Contracts\HelpDeskServicesInterface;
use App\Http\Requests\ValidateLoginRequest;

use App\Exceptions\UserNotFoundException;
use App\Exceptions\AuthenticationException;
use App\Exceptions\UserInactiveException;

use Illuminate\Http\Request;

use Exception;

class PuntosVentaController extends Controller
{   
    protected $helpDeskServiceInterface;
    public function __construct(HelpDeskServicesInterface $helpDeskServiceInterface)
    {
        $this->helpDeskServiceInterface = $helpDeskServiceInterface;
    }
    public function loginOAuth(ValidateLoginRequest $request, AuthServicesInterface $auth,HelpDeskServicesInterface $helpServices )
    {
                
        // Se valida los datos con inyección de dependencias de un validador 
        $validatedData = $request->only(['username', 'password','session' ,'plataform', 'iddroid', 'app']);

        try {
        // Puedes proceder con la lógica de autenticación
        $authResult = $auth->authenticate($validatedData);


        // Validar resultado
        if (isset($authResult['error'])) {
            if($validatedData['plataform']=="WEB"){
                return redirect()->back()->withErrors(['error' => $authResult['error']]);
            }else{
                return response()->json(['error' => $authResult['error']], 400); // Error de autenticación
            }                
        }

        // Respuesta exitosa con los datos necesarios
        if($validatedData['plataform']=="WEB"){
            return redirect()->route('dashboard')->with([
                'user' => $authResult['user']
            ]);
        }else{
            // Para la aplicación mobil se envía el ticket count directamente
            $ticketsCount= $helpServices->getTicketsCount($authResult['user']->glpi_id);

            return response()->json([
                'id' => $authResult['user']->id,
                'name' => $authResult['user']->name,
                'password' => $authResult['user']->password,
                'email' => $authResult['user']->password,
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
    } catch (UserNotFoundException $e) {
        if($validatedData['plataform']=="WEB"){
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }else{
            return response()->json(['error' => $e->getMessage()], 404);
        }        
    } catch (AuthenticationException $e) {
        if($validatedData['plataform']=="WEB"){
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }else{
            return response()->json(['error' => $e->getMessage()], 401);    
        }        
    } catch (UserInactiveException $e) {
        if($validatedData['plataform']=="WEB"){
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }else{
            return response()->json(['error' => $e->getMessage()], 403);    
        }        
    } catch (Exception $e) {
        if($validatedData['plataform']=="WEB"){
            return redirect()->back()->withErrors(['error' => 'Error interno del servidor']);
        }else{
            return response()->json(['error' => 'Error interno del servidor'], 500);   
        }
    }
    }

    public function createFastTicket(Request $request)
    {
        // Lógica para crear un ticket rápido
        $this->helpDeskServiceInterface->createTicket($request->all());
    }
}
