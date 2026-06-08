<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateServiceRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[\pL\pN\s\-\(\)·]+$/u',
            ],

            'description' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (trim(strip_tags($value)) === '') {
                        $fail('La descripció és obligatòria.');
                    }
                },
            ],

            'duration_minutes' => [
                'required',
                'integer',
                'min:1',
            ],

            'icon' => [
                'nullable',
                'string',
                'max:50',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.regex' => 'El nom només pot contenir lletres, números, espais, guions, parèntesis i punt volat (·).',
            'name.required' => 'El nom del servei és obligatori.',
            'name.max' => 'El nom no pot superar els 255 caràcters.',
            'duration_minutes.required' => 'La durada és obligatòria.',
            'duration_minutes.integer' => 'La durada ha de ser un número enter de minuts.',
            'duration_minutes.min' => 'La durada ha de ser d\'almenys 1 minut.',
        ];
    }
}
