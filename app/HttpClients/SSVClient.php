<?php

namespace App\HttpClients;

use GuzzleHttp\Client as Guzzle;

class SSVClient extends Guzzle
{
    public function __construct()
    {
        parent::__construct([
            'base_uri' => env('SSV_ENDPOINT'),
            'http_errors' => false,
        ]);
    }

    public function sendData(string $method, string $endpoint, array $data = []) : Object
    {
        if (isset($data['json'])) {
            $options['json'] = $data['json'];
        }

        $response = parent::request($method, $endpoint, $options);

        $response = json_decode($response->getBody()->getContents());

        return $response;
    }

}
