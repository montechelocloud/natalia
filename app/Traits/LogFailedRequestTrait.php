<?php

namespace App\Traits;

use App\Models\LogFailedRequest;

trait LogFailedRequestTrait
{
    /**
     * Registra las peticiones fallidas y los mensajes de error.
     * @author Edwin David Sanchez Balbin
     *
     * @param string $path
     * @param object $response
     * @param array $data
     * @return void
     */
    public function saveLogFailedRequest(string $path, object $response, array $data = [])
    {
        if ($response->status_code != 200 && $response->status_code != 201) {
            LogFailedRequest::created([
                'request_url' => $path,
                'request_data' => json_encode($data),
                'status_code' => $response->status_code,
                'messages' => $response->message ?? $response->messages,
                'detail' => $response->detail ?? $response->codigo_queja
            ]);
        }
    }
}
