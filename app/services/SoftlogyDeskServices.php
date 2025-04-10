<?php

namespace App\Services;

use App\Contracts\SoftlogyDeskServicesInterface;

use Exception;

use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

use App\Api\Gateway;


class SoftlogyDeskServices implements SoftlogyDeskServicesInterface
{

    protected $gatewayServices;

    /**
     * Create a new class instance.
     */
    public function __construct(Gateway $gatewayServices)
    {
        $this->gatewayServices=$gatewayServices;
    }
    
    public function getSessionToken()
    {
        $autorization=rtrim(config('services.gateway.autorization'));
        // 
        $headers = [
            'Authorization' => "Basic $autorization",
            'App-Token' =>  rtrim(config('services.gateway.app-token'))
        ];

        $endpoint="initSession";

        $session_token = $this->gatewayServices->request('GET', $endpoint, $headers);

        return $session_token;

    }

    public function reportStatusStore(array $data)
    {        
        return response()->json($data);
    }

    public function makeBycript(String $textToEncrypt): string
    {        

        if (empty($textToEncrypt)) {
            throw new \InvalidArgumentException("El texto a encriptar no puede estar vacío");
        }
    
        $method = 'aes-256-cbc';
        $key = env('APP_KEY');
        
        if (strlen($textToEncrypt) < 32) {
            throw new \RuntimeException("La clave de encriptación debe tener al menos 32 caracteres");
        }
    
        $ivLength = openssl_cipher_iv_length($method);
        $iv = openssl_random_pseudo_bytes($ivLength);
        
        $encrypted = openssl_encrypt($textToEncrypt, $method, $key, OPENSSL_RAW_DATA, $iv);
        
        if ($encrypted === false) {
            throw new \RuntimeException("Error al encriptar el texto: " . openssl_error_string());
        }
    
        return base64_encode($iv . $encrypted);
        
    }

    public function makeDecrypt(string $encryptedText): string
    {
        $method = 'aes-256-cbc';
        $key = env('APP_KEY');
        $data = base64_decode($encryptedText);
        $ivLength = openssl_cipher_iv_length($method);
        $iv = substr($data, 0, $ivLength);
        $encrypted = substr($data, $ivLength);
        
        return openssl_decrypt($encrypted, $method, $key, OPENSSL_RAW_DATA, $iv);
    }

}