<?php

namespace App\Traits;

use Illuminate\Support\Facades\Validator;

trait RequestValidator
{
    public function dataValidate(array $data, array $rules)
    {
        $validator = Validator::make($data, $rules);
        
        if ($validator->fails()) {
            return response()->json($validator->errors(), 200);
        }
    }
}
