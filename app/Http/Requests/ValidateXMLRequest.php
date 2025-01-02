<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidateXMLRequest extends FormRequest
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
            'xmlFactura' => 'required|max:10240', // Archivo XML requerido, máximo 10MB
            'identificator' => 'required|regex:/^\d+$/', // Identificación: solo números
            'digit' => 'required|regex:/^\d+$/',
            'phone' => 'required|numeric|min:15',
            'firstName' => 'required|string|max:255', // Primer nombre: solo texto
            'secondName' => 'nullable|string|max:255', // Segundo nombre es opcional
            'lastName' => 'required|string|max:255', // Apellidos: solo texto
            'emailReceptor' => 'required|email|max:255', // Correo electrónico: formato válido
            'pais' => 'required|exists:paises,codigo', // País: debe existir en la base de datos
            'state' => 'required|string|max:255', // Departamento/Estado: solo texto
            'city' => 'required|string|max:255', // Ciudad: solo texto
            'folio' => 'required|numeric|min:1', // Folio: debe ser numérico y mayor a 0
        ];

    }
}