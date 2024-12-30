<?php

namespace app\services;

use Illuminate\Support\Facades\DB;
use App\Contracts\AuthServicesInterface;
use App\Repositories\GlpiUserRepository;

use Exception;

class AuthServices implements AuthServicesInterface
{
    protected $userRepository;

    public function __construct(GlpiUserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    public function Authenticate(array $data)
    {

        try {

            // Busca al usuario por cualquier campo necesario, como 'username' o 'email'
            $user = $this->userRepository
            ->only(['id','name','password','realname','firstname'])
            ->where('name', $data['username'])
            ->first();

            if (!$user) {
                return ['error' => 'Usuario no encontrado'];
            }
            // Verificar la contraseña
            if (!password_verify($data['password'], $user->password)) {
                return ['error' => 'Credenciales inválidas'];
            }
            // Si no tenemos error obtenemos la información del correo correspondiente
            $statusCounts = DB::connection('mysql_second')
            ->table('glpi_useremails')        
            ->select('id', 'email')
            ->where('users_id', $user->id)
            ->first();
            // Generar un token de acceso para el usuario
            $token = $user->createToken('authToken')->plainTextToken;

            return [
                'id' => $user->id,
                'name' => $user->name,
                'token' => $token, // Token generado
            ];

        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
