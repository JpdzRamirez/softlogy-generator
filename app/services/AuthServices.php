<?php

namespace App\Services;


use App\Contracts\AuthServicesInterface;
use App\Repositories\GlpiUserRepository;

use App\Repositories\UserRepository;

use App\Exceptions\UserNotFoundException;
use App\Exceptions\AuthenticationException;
use App\Exceptions\UserInactiveException;

use Exception;

class AuthServices implements AuthServicesInterface
{
    protected $glpiUserRepository;
    protected $userRepository;

    public function __construct(GlpiUserRepository $glpiUserRepository, UserRepository $userRepository)
    {
        $this->glpiUserRepository = $glpiUserRepository;
        $this->userRepository = $userRepository;
    }
    private function getUserPicture($user)
    {
        // For unix linux
        // $resourcePath = env('SOFTLOGY_HELDEKS_PICTURES') . $user->picture;
        // For windos enviroment
        $picturePath = env('SOFTLOGY_HELDEKS_PICTURES');

        if (!empty($user->picture)) {
            $resourcePath = str_replace('/', '\\', $picturePath . $user->picture);
        
            if (file_exists($resourcePath)) {
                $imageData = file_get_contents($resourcePath);
                return 'data:image/png;base64,' . base64_encode($imageData);
            }
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
            ->only(
                ['id', 'name', 'phone', 'mobile', 'password', 'realname', 'firstname', 'profiles_id', 'entities_id', 'usertitles_id', 'locations_id', 'is_active', 'picture'],
                ['location', 'entiti', 'title', 'email','profile']
            )
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
                                🔑 Token Access Section
                ***************************************************                    
             */
            $expiration=false;
            if($data['plataform'] == "WEB"){
                $expiration = isset($data['session']) ? (bool)$data['session'] : false;
            }else{
                $expiration = now()->addMinutes((int) config('sanctum.expiration', 1));
            }
            

            $response = $this->glpiUserRepository->setTokenUserSession($user, $expiration,$data['plataform'],$this->userRepository);

            // Return validated user with token
            return $response;

        } catch (UserNotFoundException | AuthenticationException | UserInactiveException $e) {
            return ['error' => $e->getMessage()];
        } catch (Exception $e) {
            return ['error' => "Ha ocurrido un error inesperado. {$e->getMessage()}"];
        }
    }

    public function makeBycript(String $textToEncrypt): array
    {        

        if (empty($textToEncrypt)) {
            throw new \InvalidArgumentException("El texto a encriptar no puede estar vacío");
        }
    
        $method = 'aes-256-cbc';
        $key = env('APP_KEY');        
    
        $ivLength = openssl_cipher_iv_length($method);
        $iv = openssl_random_pseudo_bytes($ivLength);
        
        $encrypted = openssl_encrypt($textToEncrypt, $method, $key, OPENSSL_RAW_DATA, $iv);
        
        if ($encrypted === false) {
            throw new \RuntimeException("Error al encriptar el texto: " . openssl_error_string());
        }
    
        return [
            'encripted'=>base64_encode($iv . $encrypted)
        ];
        
    }

    public function makeDecrypt(string $encryptedText): array
    {
        $method = 'aes-256-cbc';
        $key = env('APP_KEY');
        $data = base64_decode($encryptedText);
        $ivLength = openssl_cipher_iv_length($method);
        $iv = substr($data, 0, $ivLength);
        $encrypted = substr($data, $ivLength);        

        return [
            'desencripted'=>openssl_decrypt($encrypted, $method, $key, OPENSSL_RAW_DATA, $iv)
        ];
    }
}
