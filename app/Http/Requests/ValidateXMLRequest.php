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
            'tipeDocument' => 'required|in:1,2,3,4',
            'xmlFactura' => 'required|file|max:10240', // Archivo XML requerido, máximo 10MB
            'identificator' => 'nullable|regex:/^\d+$/', // Identificación: solo números
            'digit' => 'nullable|regex:/^\d+$/',
            'phone' => 'nullable|numeric|min:15',
            'firstName' => 'nullable|string|max:255', // Primer nombre: solo texto
            'secondName' => 'nullable|string|max:255', // Segundo nombre es opcional
            'lastName' => 'nullable|string|max:255', // Apellidos: solo texto
            'emailReceptor' => 'nullable|email|max:255', // Correo electrónico: formato válido
            'country' => 'required|exists:paises,codigo', // País: debe existir en la base de datos
            'state' => 'nullable|string|max:255', // Departamento/Estado: solo texto            
            'city' => 'nullable|string|max:255', // Ciudad: solo texto
            'address' => 'nullable|string|max:255', // Dirección específica de residencia
            'postalCode' => 'nullable|numeric|min:1', // Folio: debe ser numérico y mayor a 0
            'folio' => 'nullable|numeric|min:1', // Folio: debe ser numérico y mayor a 0
        ];

    }
}