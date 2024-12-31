<?php

namespace app\services;

use Illuminate\Support\Facades\DB;
use App\Contracts\AuthServicesInterface;
use App\Repositories\GlpiUserRepository;
use App\Repositories\GlpiQueryRepository;

use Exception;

class AuthServices implements AuthServicesInterface
{
    protected $glpiUserRepository;
    protected $queryRepository;

    public function __construct(GlpiUserRepository $glpiUserRepository,GlpiQueryRepository $queryRepository)
    {
        $this->glpiUserRepository = $glpiUserRepository;
        $this->queryRepository = $queryRepository;
    }
    public function Authenticate(array $data)
    {

        try {

            // Busca al usuario por cualquier campo necesario, como 'username' o 'email'
            $user = $this->glpiUserRepository
            ->only(['id','name','password','realname','firstname','picture'])
            ->where('name', $data['username'])
            ->first();

            if (!$user) {
                return ['error' => 'Usuario no encontrado'];
            }
            // Verificar la contraseña
            if (!password_verify($data['password'], $user->password)) {
                return ['error' => 'Credenciales inválidas'];
            }
            $resoursesFile = env('SOFTLOGY_HELDEKS_PICTURES');
            $completePath = $resoursesFile . $user->picture;

            if (file_exists($completePath)) {
                $imageData = file_get_contents($completePath);
                $imagesBase64[] = 'data:image/png'. ';base64,' . base64_encode($imageData);
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
