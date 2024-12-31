<?php

namespace app\services;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
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

            /*  
                ***************************************************
                            👩‍💻 Validate Oauth GLPI User
                ***************************************************                    
             */
            // Busca al usuario por cualquier campo necesario, como 'username' o 'email'
            $user = $this->glpiUserRepository
            ->only(['id','name','phone','mobile','password','realname','firstname','picture'])
            ->where('name', $data['username'])
            ->first();

            if (!$user) {
                return ['error' => 'Usuario no encontrado'];
            }
            // Verificar la contraseña
            if (!password_verify($data['password'], $user->password)) {
                return ['error' => 'Credenciales inválidas'];
            }

            /*  
                ***************************************************
                                📊 PICTURE SECTION
                ***************************************************                    
             */
            // Agregamos la ruta por defecto del recurso fisico
            $resoursesFile = env('SOFTLOGY_HELDEKS_PICTURES');

            // Concatenamos con el path de picture
            $completePath = $resoursesFile . $user->picture;
            // Validamos si existe el recurso
            if (file_exists($completePath)) {
                    $imageData = file_get_contents($completePath);
                    $imagesBase64 = 'data:image/png;base64,' . base64_encode($imageData);  
                    $user->picture= $imagesBase64;            
            }else{
                // Ruta de la imagen predeterminada
                $defaultImagePath = public_path('assets/img/user.png');
                
                if (file_exists($defaultImagePath)) {
                    $defaultImageData = file_get_contents($defaultImagePath);
                    $imagesBase64 = 'data:image/png;base64,' . base64_encode($defaultImageData);  
                } else {
                    // Caso donde no se encuentra la imagen predeterminada
                    $imagesBase64 = null; 
                }
                
                $user->picture = $imagesBase64;
            }
            /*  
                ***************************************************
                                🔏 Password Section
                ***************************************************                    
             */
            $hashedPassword=Hash::make(Str::random(12));

            $user->password = $hashedPassword;
            // Si no tenemos error obtenemos la información del correo correspondiente
            $userEmail = $this->queryRepository->getUserEmailById($user->id);

            /*  
                ***************************************************
                                🔑 Token Access Section
                ***************************************************                    
             */
            $response = $this->queryRepository->setTokenUserSession($user, $userEmail);

            // Return validated user with token
            return $response;

        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
