<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDireccionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
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
        return [
            'nombre_destinatario' => 'required|string|max:100',
            'telefono' => 'required|string|max:20|regex:/^[\d\s\-\+\(\)]+$/',
            'calle' => 'required|string|max:255',
            'numero' => 'required|string|max:20',
            'piso' => 'nullable|string|max:20',
            'departamento' => 'nullable|string|max:100',
            'codigo_postal' => 'required|string|max:10|regex:/^[\d\w\s\-]+$/',
            'ciudad' => 'required|string|max:100',
            'provincia' => 'required|string|max:100',
            'pais' => 'nullable|string|max:100',
            'referencias' => 'nullable|string|max:500',
            'predeterminada' => 'nullable|boolean',
            'tipo_direccion' => 'nullable|string|in:casa,apartamento,trabajo,otro',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nombre_destinatario.required' => 'El nombre del destinatario es obligatorio.',
            'nombre_destinatario.string' => 'El nombre del destinatario debe ser texto.',
            'nombre_destinatario.max' => 'El nombre del destinatario no puede exceder 100 caracteres.',
            'telefono.required' => 'El teléfono es obligatorio.',
            'telefono.string' => 'El teléfono debe ser texto.',
            'telefono.max' => 'El teléfono no puede exceder 20 caracteres.',
            'telefono.regex' => 'El formato del teléfono no es válido.',
            'calle.required' => 'La calle es obligatoria.',
            'calle.string' => 'La calle debe ser texto.',
            'calle.max' => 'La calle no puede exceder 255 caracteres.',
            'numero.required' => 'El número es obligatorio.',
            'numero.string' => 'El número debe ser texto.',
            'numero.max' => 'El número no puede exceder 20 caracteres.',
            'piso.string' => 'El piso debe ser texto.',
            'piso.max' => 'El piso no puede exceder 20 caracteres.',
            'departamento.string' => 'El departamento debe ser texto.',
            'departamento.max' => 'El departamento no puede exceder 100 caracteres.',
            'codigo_postal.required' => 'El código postal es obligatorio.',
            'codigo_postal.string' => 'El código postal debe ser texto.',
            'codigo_postal.max' => 'El código postal no puede exceder 10 caracteres.',
            'codigo_postal.regex' => 'El formato del código postal no es válido.',
            'ciudad.required' => 'La ciudad es obligatoria.',
            'ciudad.string' => 'La ciudad debe ser texto.',
            'ciudad.max' => 'La ciudad no puede exceder 100 caracteres.',
            'provincia.required' => 'La provincia es obligatoria.',
            'provincia.string' => 'La provincia debe ser texto.',
            'provincia.max' => 'La provincia no puede exceder 100 caracteres.',
            'pais.string' => 'El país debe ser texto.',
            'pais.max' => 'El país no puede exceder 100 caracteres.',
            'referencias.string' => 'Las referencias deben ser texto.',
            'referencias.max' => 'Las referencias no pueden exceder 500 caracteres.',
            'predeterminada.boolean' => 'El campo predeterminada debe ser verdadero o falso.',
            'tipo_direccion.string' => 'El tipo de dirección debe ser texto.',
            'tipo_direccion.in' => 'El tipo de dirección debe ser: casa, apartamento, trabajo u otro.',
        ];
    }
}
