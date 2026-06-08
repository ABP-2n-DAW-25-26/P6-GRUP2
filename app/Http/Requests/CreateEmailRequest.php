<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateEmailRequest extends FormRequest
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
            'email' => 'required|email|unique:emails,email|max:255',
            'active' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'L\'adreça de correu és obligatòria.',
            'email.email' => 'L\'adreça de correu no té un format vàlid.',
            'email.unique' => 'Aquesta adreça de correu ja està registrada.',
            'email.max' => 'L\'adreça de correu no pot superar els 255 caràcters.',
        ];
    }
}
