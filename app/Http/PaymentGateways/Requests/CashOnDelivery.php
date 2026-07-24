<?php

namespace App\Http\PaymentGateways\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CashOnDelivery extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
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
     * @return array
     */
    public function rules(): array
    {
        return [
            'cash_on_delivery_status' => ['nullable', 'numeric'],
            'payment_type'            => ['required', 'string'],
        ];
    }
}
