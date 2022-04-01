<?php

namespace App\Http\Clients;

use App\Traits\LogFailedRequestTrait;
use GuzzleHttp\Client as Guzzle;

class SFCClient
{
    use LogFailedRequestTrait;

    protected $client;

    public function __construct(bool $withToken = true)
    {
        $this->client = new Guzzle([
            'base_uri' => env('SFC_ENDPOINT'),
            'http_errors' => false,
            'headers' => $this->changeHeaders($withToken)
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
            $headers['Authorization'] = 'Bearer ' . session('access')['token'];
        }
        
        if (isset($data['json'])) {
            $options['json'] = $data['json'];
            $headers['X-SFC-Signature'] = $this->getSignature($data['json']);
        } elseif (isset($data['payload'])) {
            $headers['X-SFC-Signature'] = $this->getSignature($data['payload']);
        } else {
            $headers['X-SFC-Signature'] = $this->getSignature(env('SFC_ENDPOINT') . $endpoint);
        }
        
        if (isset($data['multipart'])) {
            $options['multipart'] = $data['multipart'];
        }
        
        $options['headers'] = $headers;
        
        // dd($options, env('SFC_ENDPOINT') . $endpoint);
        $response = $this->client->request($method, $endpoint, $options);
        $statusCode = $response->getStatusCode();
        $response = json_decode($response->getBody()->getContents());

        if ($statusCode == 500 && is_null($response)) {
            $response = (object) ['error' => 'Internal Server Error'];
        }

        if ($statusCode != 200 && $statusCode != 201) {
            $this->saveLogFailedRequest($statusCode, $response, $data);
        }

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
        $signature = hash_hmac('SHA256', $this->dataAsString($payload), env('SFC_SECRET_KEY'));

        return strtoupper($signature);
    }

    /**
     * Pasa los arreglos a un json codificado como string.
     *
     * @param mixed $data
     * @return string
     */
    private function dataAsString($data) : string
    {
        if (gettype($data) == 'array') {
            $data = json_encode($data);
            $data = str_replace('":', '": ', $data);
            $data = str_replace(',"', ', "', $data);
        }

        return $data;
    }

    private function changeHeaders(bool $withToken) : array
    {
        $headers = [
            'Cache-Control' => 'no-cache',
            'Lenguage' => 'es-CO',
            'X-SFC-Signature' => '',
            'Content-type' => ''
        ];

        if ($withToken) {
            $headers = array_merge($headers, ['Authorization' => '']);
        }

        return $headers;
    }
}