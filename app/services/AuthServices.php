<?php

namespace app\services;


use App\Contracts\AuthServicesInterface;
use App\Repositories\GlpiUserRepository;
use App\Repositories\GlpiQueryRepository;

use App\Exceptions\UserNotFoundException;
use App\Exceptions\AuthenticationException;
use App\Exceptions\UserInactiveException;

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
    private function getUserPicture($user)
    {
        // For unix linux
        // $resourcePath = env('SOFTLOGY_HELDEKS_PICTURES') . $user->picture;
        // For windos enviroment
        $resourcePath = str_replace('/', '\\', env('SOFTLOGY_HELDEKS_PICTURES') . $user->picture);
        if (file_exists($resourcePath)) {
            $imageData = file_get_contents($resourcePath);
            return 'data:image/png;base64,' . base64_encode($imageData);
        }

        $defaultImagePath = public_path('assets/img/user.png');
        if (file_exists($defaultImagePath)) {
            $defaultImageData = file_get_contents($defaultImagePath);
            return 'data:image/png;base64,' . base64_encode($defaultImageData);
        }

        return null;
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
            ->only(['id','name','phone','mobile','password','realname','firstname','is_active','picture'])
            ->where('name', $data['username'])
            ->first();

            if (!$user) {
                throw new UserNotFoundException();
            }
            
            if (!password_verify($data['password'], $user->password)) {
                throw new AuthenticationException();
            }
            
            if ($user->is_active == 0) {
                throw new UserInactiveException();
            }
            // Si no tenemos error constriumos el usuario para iniciar sesión
            /*  
                ***************************************************
                                📊 PICTURE SECTION
                ***************************************************                    
             */
            $userImage= $this->getUserPicture($user);
            $user->picture=$userImage;
            /*  
                ***************************************************
                                🔏 Password Section
                ***************************************************                    
             */
            
            $userEmail = $this->queryRepository->getUserEmailById($user->id);

            /*  
                ***************************************************
                                🔑 Token Access Section
                ***************************************************                    
             */
            $response = $this->queryRepository->setTokenUserSession($user, $userEmail->email);

            // Return validated user with token
            return $response;

        } catch (UserNotFoundException | AuthenticationException | UserInactiveException $e) {
            return ['error' => $e->getMessage()];
        } catch (Exception $e) {
            return ['error' => 'Ha ocurrido un error inesperado.'];
        }
    }
}
