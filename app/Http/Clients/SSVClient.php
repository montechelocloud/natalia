<?php

namespace App\Http\Clients;

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

    /**
     * Envia datos al SSV
     * @author Edwin David Sanchez Balbin
     *
     * @param string $method
     * @param string $endpoint
     * @param array $data
     * @return Object
     */
    public function sendData(string $method, string $endpoint, array $data = []) : Object
    {
        $options = [];
        if (isset($data['json'])) {
            $options['json'] = $data['json'];
        }

        $response = parent::request($method, $endpoint, $options);

        $response = json_decode($response->getBody()->getContents());

        return $response;
    }

}
