<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidateLoginRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para hacer esta solicitud.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {   
        //Reglas de validación para actualización de datos pos-registro
        return [            
            'username' => 'required|string|not_regex:/^\s*$/',
            'password' => 'required|string|not_regex:/^\s*$/',      
            'plataforma' => 'required',
            'iddroid' => 'required',
            'app' => 'required',   
        ];

    }
}