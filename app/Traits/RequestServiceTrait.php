<?php

namespace App\Traits;

use App\Models\LogFailedRequest;

trait RequestServiceTrait
{
    use SignatureTrait;

    /**
     * Realiza las peticiones a la SFC (Superintendencia Financiera de Colombia)
     * @author Edwin David Sanchez Balbin
     *
     * @param string $method
     * @param string $endpoint
     * @param array $data
     * @param boolean $withToken
     * @return Object
     */
    public function jsonRequest(string $method, string $endpoint, array $data = [], bool $withToken = true) : Object
    {
        $headers = ['X-SFC-Signature' => $this->getSignature()];

        if ($withToken && session()->has('access')) {
            $headers['Authorization'] = 'Bearer ' . session('access');
        }

        $options = ['headers' => $headers];

        if (count($data)) {
            $options['json'] = $data;
        }

        $response = $this->client->request($method, $endpoint, $options);

        $response = json_decode($response->getBody()->getContents());

        return $response;
    }
}
