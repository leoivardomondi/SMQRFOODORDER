<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MailTestRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() : bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() : array
    {
        return [
            'email'           => ['required', 'email', 'max:190'],
            'sample_type'     => ['required', 'string', 'in:branch_manager_order,customer_order_confirmation,customer_order_out_for_delivery,smtp_connection_test'],
            'order_id'        => ['nullable', 'integer'],
            'branch_id'       => ['nullable', 'integer'],
            'mail_host'       => ['nullable', 'string', 'max:190'],
            'mail_port'       => ['nullable', 'string', 'max:190'],
            'mail_username'   => ['nullable', 'string', 'max:190'],
            'mail_password'   => ['nullable', 'string', 'max:190'],
            'mail_encryption' => ['nullable', 'string', 'max:190'],
            'mail_from_name'  => ['nullable', 'string', 'max:190'],
            'mail_from_email' => ['nullable', 'string', 'max:190'],
        ];
    }
}
