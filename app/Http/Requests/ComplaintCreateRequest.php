<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ComplaintCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'codigo_queja'      => 'required|string',
            'codigo_pais'       => 'required|string',
            'departamento_cod'  => 'required|string',
            'municipio_cod'     => 'required|string',
            'canal_cod'         => 'required|numeric',
            'producto_cod'      => 'required|numeric',
            'macro_motivo_cod'  => 'required|numeric',
            'fecha_creacion'    => 'required|date',
            'nombres'           => 'required|string',
            'tipo_id_CF'        => 'required|numeric',
            'numero_id_CF'      => 'required|string',
            'tipo_persona'      => 'required|numeric',
            'insta_recepcion'   => 'required|numeric',
            'punto_recepcion'   => 'required|numeric',
            'admision'          => 'required|numeric',
            'texto_queja'       => 'required|string',
            'anexo_queja'       => 'required|boolean',
            'ente_control'      => 'required|numeric',
        ];
    }
}
