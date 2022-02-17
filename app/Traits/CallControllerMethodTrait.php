<?php

namespace App\Traits;

trait CallControllerMethodTrait
{
    /**
     * Llama al metodo del controlador solicitado y le pasa los datos como parametro
     * @author Edwin David Sanchez Balbin
     *
     * @param string $controller
     * @param string $method
     * @param mixed $data
     * @return mixed
     */
    public function callControllerMethod(string $controller, string $method, mixed $data)
    {
        $controller = ucfirst($controller);
        $controller = "App\Http\Controllers\\{$controller}";

        return call_user_func_array([new $controller, $method], $data);
    }
}
