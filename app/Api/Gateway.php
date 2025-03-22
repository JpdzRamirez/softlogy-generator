<?php

namespace App\Api;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class Gateway 
{
    protected $client;
    protected $baseUrl;
    protected $defaultHeaders;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.gateway.url'), '/'); // Eliminar barra final
        $this->defaultHeaders = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'App-Token' =>  rtrim(config('services.gateway.app-token')) // Token opcional en .env
        ];
        $this->client = new Client([
            'timeout' => 10.0, // Timeout opcional
        ]);
    }

    /**
     * Realiza una petición HTTP dinámica.
     * 
     * @param string $method    Método HTTP (GET, POST, PUT, PATCH, DELETE)
     * @param string $endpoint  Endpoint a consumir (sin la URL base)
     * @param array  $headers   Headers opcionales (sobrescribe los globales)
     * @param array  $body      Cuerpo de la petición (JSON o form-data)
     * @return array            Respuesta en formato array
     */
    public function request(string $method, string $endpoint, array $headers = [], array $body = [])
    {
        try {
            // Construir URL completa
            $url = $this->baseUrl . '/' . ltrim($endpoint, '/');

            // Fusionar headers globales con los personalizados (permitiendo sobrescribir)
            $mergedHeaders = array_merge($this->defaultHeaders, $headers);

            // Configurar opciones de la petición
            $options = [
                'headers' => $mergedHeaders,
                'verify' => false,
            ];

            if (!empty($body)) {
                $options['json'] = $body; // Envía JSON en la petición
            }

            // 📌 **Aquí se hace la petición HTTP con Guzzle**
            $response = $this->client->request($method, $url, $options);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            Log::error("Error en Gateway: " . $e->getMessage());
            return [
                'error' => true,
                'message' => $e->getMessage(),
                'status' => $e->getCode(),
            ];
        }
    }

    /**
     * Permite actualizar los headers globales en tiempo de ejecución.
     * 
     * @param array $headers
     */
    public function setHeaders(array $headers)
    {
        $this->defaultHeaders = array_merge($this->defaultHeaders, $headers);
    }
}
