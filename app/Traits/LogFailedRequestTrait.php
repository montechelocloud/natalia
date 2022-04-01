<?php

namespace App\Traits;

use App\Models\LogFailedRequest;

trait LogFailedRequestTrait
{
    /**
     * Registra las peticiones fallidas y los mensajes de error.
     * @author Edwin David Sanchez Balbin
     *
     * @param object $response
     * @param array $data
     * @return void
     */
    public function saveLogFailedRequest($statusCode, object $response, array $data = [])
    {
        // dd($response, $response->message ?? $response->messages ?? $response->error);
        LogFailedRequest::created([
            'request_data' => json_encode($data),
            'status_code' => $statusCode,
            'messages' => $response->message ?? $response->messages ?? $response->error,
            'detail' => $response->detail ?? $response->codigo_queja ?? ''
        ]);
    }
}
