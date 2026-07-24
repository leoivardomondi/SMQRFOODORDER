<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WhatsappApiRequest extends FormRequest
{
    public function authorize() : bool
    {
        return true;
    }

    public function rules() : array
    {
        return [
            'whatsapp_phone_number_id' => ['nullable', 'string', 'max:190'],
            'whatsapp_access_token'    => ['nullable', 'string', 'max:1000'],
            'whatsapp_template_name'   => ['nullable', 'string', 'max:190'],
            'whatsapp_recipient_phone' => ['nullable', 'string', 'max:190'],
            'whatsapp_status'          => ['required', 'integer', Rule::in([5, 10])],
        ];
    }
}
