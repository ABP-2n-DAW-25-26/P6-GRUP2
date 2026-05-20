<?php

namespace App\Http\Requests;

use App\Rules\TurnstileRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreAppointmentRequest extends FormRequest
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
            'service_id' => [
                'required',
                'exists:services,id',
            ],

            'customer_name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[\pL\s\'-]+$/u',
            ],

            'customer_phone' => [
                'required',
                'regex:/^[0-9]{9}$/',
            ],

            'customer_email' => [
                'required',
                'email',
                'max:255',
            ],

            'appointment_date' => [
                'required',
                'date',
            ],

            'start_time' => [
                'required',
                'date_format:H:i',
            ],

            'cf-turnstile-response' => app()->environment('testing')
                ? ['nullable']
                : ['required', 'string', new TurnstileRule],
        ];
    }

    public function messages(): array
    {
        return [
            'service_id.required' => 'Has de seleccionar un servei.',
            'service_id.exists' => 'El servei seleccionat no és vàlid.',
            'customer_name.required' => 'El nom és obligatori.',
            'customer_name.string' => 'El nom ha de ser text.',
            'customer_name.max' => 'El nom no pot tenir més de 255 caràcters.',
            'customer_name.regex' => 'El nom només pot contenir lletres, espais, apòstrofs i guions.',
            'customer_phone.required' => 'El telèfon és obligatori.',
            'customer_phone.regex' => 'El telèfon ha de tenir 9 dígits.',
            'customer_email.required' => 'El correu electrònic és obligatori.',
            'customer_email.email' => 'El correu electrònic no és vàlid.',
            'customer_email.max' => 'El correu electrònic no pot tenir més de 255 caràcters.',
            'appointment_date.required' => 'La data és obligatòria.',
            'appointment_date.date' => 'La data no és vàlida.',
            'start_time.required' => 'L\'hora és obligatòria.',
            'start_time.date_format' => 'El format de l\'hora no és vàlid.',
            'cf-turnstile-response.required' => 'Si us plau, completa la verificació.',
        ];
    }
}
