<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderSetupRequest extends FormRequest
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
            'order_setup_food_preparation_time'        => ['required', 'numeric'],
            'order_setup_schedule_order_slot_duration' => ['required', 'numeric'],
            'order_setup_takeaway'                     => ['required', 'numeric'],
            'order_setup_delivery'                     => ['required', 'numeric'],
            'order_setup_free_delivery_kilometer'      => ['required', 'numeric'],
            'order_setup_basic_delivery_charge'        => ['required', 'numeric'],
            'order_setup_charge_per_kilo'              => ['required', 'numeric'],
            'order_setup_max_delivery_kilometer'       => ['nullable', 'numeric'],
            'order_setup_trust_score_enable'           => ['required', 'numeric'],
            'order_setup_trust_score_min_orders'       => ['required', 'numeric'],
        ];
    }
}