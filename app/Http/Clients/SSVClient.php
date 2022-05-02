<?php

namespace App\Http\Clients;

use App\Traits\LogFailedRequestTrait;
use GuzzleHttp\Client as Guzzle;

class SSVClient
{
    use LogFailedRequestTrait;
    
    protected $client;

    public function __construct()
    {
        $this->client = new Guzzle([
            'base_uri' => env('SSV_ENDPOINT'),
            'http_errors' => false,
        ]);
    }

    /**
     * Envia datos al SSV
     * @author Edwin David Sanchez Balbin <e.sanchez@montechelo.com.co>
     *
     * @param string $method
     * @param string $endpoint
     * @param array $data
     * @return object
     */
    public function sendData(string $method, string $endpoint, array $data = []) : object
    {
        $options = [];
        if (isset($data['json'])) {
            $options['json'] = $data['json'];
        }

        $response = $this->client->request($method, $endpoint, $options);
        
        $statusCode = $response->getStatusCode();
        $response = json_decode($response->getBody()->getContents());

        if ($statusCode == 500 || is_null($response)) {
            $response = (object) ['error' => 'Internal Server Error'];
        }

        if ($statusCode != 200 && $statusCode != 201) {
            $this->saveLogFailedRequest($statusCode, $response, $data);
        }

        return $response;
    }

}
