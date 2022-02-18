<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ComplaintUpdateRequest extends FormRequest
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
            'codigo_queja'              => 'required|string',
            'sexo'                      => 'required|numeric',
            'lgbtiq'                    => 'required|numeric',
            'condicion_especial'        => 'required|numeric',
            'canal_cod'                 => 'required|numeric',
            'producto_cod'              => 'required|numeric',
            'macro_motivo_cod'          => 'required|numeric',
            'estado_cod'                => 'required|numeric',
            'fecha_actualizacion'       => 'required|date',
            'producto_digital'          => 'required|numeric',
            'a_favor_de'                => 'required|numeric',
            'aceptacion_queja'          => 'required|numeric',
            'rectificacion_queja'       => 'required|numeric',
            'desistimiento_queja'       => 'required|numeric',
            'prorroga_queja'            => 'required|numeric',
            'admision'                  => 'required|numeric',
            'documentacion_rta_final'   => 'required|boolean',
            'anexo_queja'               => 'required|boolean',
            'fecha_cierre'              => 'required|date',
            'tutela'                    => 'required|numeric',
            'ente_control'              => 'required|numeric',
            'marcacion'                 => 'required|numeric',
            'queja_expres'              => 'required|numeric',
        ];
    }
}
