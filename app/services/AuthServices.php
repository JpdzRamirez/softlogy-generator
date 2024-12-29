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
            $user = $this->userRepository->all()->where('name', $data['username'])->first();

            if (!$user) {
                return ['error' => 'Usuario no encontrado'];
            }
            // Verificar la contraseña
            if (!password_verify($data['password'], $user->password)) {
                return ['error' => 'Credenciales inválidas'];
            }

            // Devuelve el usuario autenticado
            return $user;

        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
