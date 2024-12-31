<?php

namespace app\services;

use Illuminate\Support\Facades\DB;
use App\Contracts\AuthServicesInterface;
use App\Repositories\GlpiUserRepository;
use App\Repositories\GlpiQueryRepository;

use Exception;

class AuthServices implements AuthServicesInterface
{
    protected $userRepository;
    protected $queryRepository;

    public function __construct(GlpiUserRepository $userRepository,GlpiQueryRepository $queryRepository)
    {
        $this->userRepository = $userRepository;
        $this->queryRepository = $queryRepository;
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
            $userEmail = $this->queryRepository->getUserEmailById($user->id);

            // Generar un token de acceso para las sesion del usuario
            $response = $this->queryRepository->setTokenUserSession($user, $userEmail);

            return $response;

        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
