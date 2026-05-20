<?php

namespace App\Http\Requests;

use App\Rules\TurnstileRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreWorkshopInscriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $workshopId = (int) $this->route('workshop')?->id;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[\pL\s\'-]+$/u',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('workshop_inscriptions', 'email')
                    ->where(fn($q) => $q->where('workshop_id', $workshopId)),
            ],

            'phone' => [
                'required',
                'regex:/^(?:\+34)?[0-9]{9}$/',
            ],

            'cf-turnstile-response' => config('services.turnstile.site_key')
                ? ['required', 'string', new TurnstileRule]
                : ['nullable'],

        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nom és obligatori.',
            'name.regex' => 'El nom només pot contenir lletres, espais, guions i apòstrofs.',

            'email.required' => 'L\'adreça electrònica és obligatòria.',
            'email.email' => 'L\'adreça electrònica no té un format vàlid.',
            'email.unique' => 'Ja estàs inscrit/a a aquest taller amb aquest correu.',

            'phone.required' => 'El telèfon és obligatori.',
            'phone.regex' => 'El telèfon ha de tenir 9 dígits (opcionalment amb prefix +34).',

            'cf-turnstile-response.required' => 'Si us plau, completa la verificació.',
        ];
    }
}
