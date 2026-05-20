<?php

namespace App\Http\Requests;

use App\Rules\TurnstileRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateAssignmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // For anyone even without an account
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'regex:/^[\pL\s\'-]+$/u'],
            'address' => 'required|email|max:255',
            'phone_number' => 'required|regex:/^[0-9]{9}$/',
            'description' => 'required|string|max:2000',
            'cf-turnstile-response' => ['required', 'string', new TurnstileRule],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nom és obligatori.',
            'name.string' => 'El nom ha de ser text.',
            'name.max' => 'El nom no pot tenir més de 255 caràcters.',
            'name.regex' => 'El nom només pot contenir lletres, espais, apòstrofs i guions.',
            'address.required' => 'El correu electrònic és obligatori.',
            'address.email' => 'El correu electrònic no és vàlid.',
            'address.max' => 'El correu electrònic no pot tenir més de 255 caràcters.',
            'phone_number.required' => 'El telèfon és obligatori.',
            'phone_number.regex' => 'El telèfon ha de tenir 9 dígits.',
            'description.required' => 'La descripció és obligatòria.',
            'description.string' => 'La descripció ha de ser text.',
            'description.max' => 'La descripció no pot tenir més de 2000 caràcters.',
            'cf-turnstile-response.required' => 'Si us plau, completa la verificació de seguretat.',
            'cf-turnstile-response.string' => 'La verificació de seguretat no és vàlida.',
        ];
    }

    public function attributes(): array
    {
        return [
            'cf-turnstile-response' => 'verificació de seguretat',
        ];
    }
}
