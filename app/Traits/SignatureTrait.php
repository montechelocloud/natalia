<?php

namespace App\Traits;

trait SignatureTrait
{
    /**
     * Genera la firma de las peticiones.
     * @author Edwin David Sanchez Balbin
     *
     * @return string
     */
    private function getSignature() : string
    {
        $header = json_encode(['alg' => 'HS256', 'typ' => 'JWT']);
        $header = base64_encode($header);

        if (gettype($this->payload) == 'array') {
            $payload = json_encode($this->payload);
        } else if (gettype($this->payload) == 'string') {
            $payload = ['endpoint' => $this->payload];
        }

        $payload = base64_encode($payload);

        $signature = hash_hmac('SHA256', "$header.$payload", env('SFC_SECRET_KEY'));


        // return "$header.$payload.$signature";
        return $signature;
    }
}
