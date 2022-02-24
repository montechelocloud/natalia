<?php

namespace App\Http\Clients;

use GuzzleHttp\Client as Guzzle;

class SFCClient
{
    protected $client;

    public function __construct()
    {
        $this->client = new Guzzle([
            'base_uri' => env('SFC_ENDPOINT'),
            'http_errors' => false,
            'headers' => [
                'Cache-Control' => 'no-cache',
                'Lenguage' => 'es-CO',
                'X-SFC-Signature' => ''
            ]
        ]);
    }

    /**
     * Envia datos a la SFC
     * @author Edwin David Sanchez Balbin
     *
     * @param string $method
     * @param string $endpoint
     * @param array $data
     * @param boolean $withToken
     * @return Object
     */
    public function sendData(string $method, string $endpoint, array $data = [], bool $withToken = true) : Object
    {
        $headers = [];
        $options = [];

        if ($withToken && session()->has('access')) {
            $headers['Authorization'] = 'Bearer ' . session('access');
        }
        
        if (isset($data['json'])) {
            $headers['Content-type'] = 'aplication/json';
            $options['json'] = $data['json'];
            $headers['X-SFC-Signature'] = $this->getSignature($data['json']);
        } elseif (isset($data['payload'])) {
            $headers['X-SFC-Signature'] = $this->getSignature($data['payload']);
        } else {
            $headers['X-SFC-Signature'] = $this->getSignature("api/$endpoint");
        }

        if (isset($data['multipart'])) {
            $headers['Content-type'] = 'multipart/form-data';
            $options['multipart'] = $data['multipart'];
        }

        $options['headers'] = $headers;

        $response = $this->client->request($method, $endpoint, $options);
        $response = json_decode($response->getBody()->getContents());

        return $response;
    }

    /**
     * Genera la firma de las peticiones.
     * @author Edwin David Sanchez Balbin
     *
     * @return string
     */
    private function getSignature($payload) : string
    {
        $header = json_encode(['alg' => 'HS256', 'typ' => 'JWT']);
        $header = base64_encode($header);

        if (gettype($payload) == 'array') {
            $payload = json_encode($payload);
        } else if (gettype($payload) == 'string') {
            $payload = ['endpoint' => $payload];
        }
        $payload = json_encode($payload);
        $payload = base64_encode($payload);

        $signature = hash_hmac('SHA256', "$header.$payload", env('SFC_SECRET_KEY'));

        // return "$header.$payload.$signature";
        return $signature;
    }
}
