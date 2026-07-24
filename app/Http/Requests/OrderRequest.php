<?php

namespace App\Http\Requests;

use App\Enums\Activity;
use App\Enums\OrderType;
use App\Rules\ValidJsonOrder;
use Illuminate\Validation\Rule;
use Smartisan\Settings\Facades\Settings;
use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
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
            'branch_id'        => ['required', 'numeric'],
            'subtotal'         => ['required', 'numeric'],
            'discount'         => ['nullable', 'numeric'],
            'delivery_charge'  => request('order_type') === OrderType::DELIVERY ? [
                'required',
                'numeric'
            ] : ['nullable'],
            'total'            => ['required', 'numeric'],
            'order_type'       => ['required', 'numeric'],
            'is_advance_order' => ['required', 'numeric'],
            'address_id'       => request('order_type') === OrderType::DELIVERY ? [
                'required',
                'numeric'
            ] : ['nullable'],
            'delivery_time'    => request('order_type') === OrderType::DELIVERY ? [
                'required',
                'string'
            ] : ['nullable'],
            'coupon_id'        => ['nullable', 'numeric'],
            'source'           => ['required', 'numeric'],
            'payment_method'   => ['nullable'],
            'customer_email'   => ['nullable', 'email', 'max:255'],
            'items'            => ['required', 'json', new ValidJsonOrder]
        ];
    }

    public function messages(): array
    {
        return [
            'address_id.required'      => 'Add your delivery address',
            'branch_id.required'       => 'Select your branch',
            'delivery_time.required'   => 'Select your preferred delivery time',
            'order_type.required'      => 'Select order type (delivery/takeaway)',
            'payment_method.required'  => 'Select your payment method',
            'items.required'           => 'Add items to your cart before ordering',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (request('order_type') == OrderType::DELIVERY && Settings::group('order_setup')->get("order_setup_delivery") == Activity::DISABLE) {
                $validator->errors()->add('order_type', 'This order type is disabled now you can try another order type right now or call the management.');
            } else if (request('order_type') == OrderType::TAKEAWAY && Settings::group('order_setup')->get("order_setup_takeaway") == Activity::DISABLE) {
                $validator->errors()->add('order_type', 'This order type is disabled now you can try another order type right now or call the management.');
            } else if (blank(request('order_type'))) {
                $validator->errors()->add('order_type', 'This order type is disabled now you can try another order type right now or call the management.');
            }

            $paymentMethod = request('payment_method');
            if ($paymentMethod === 'cash' || $paymentMethod === 'cash-on-delivery' || $paymentMethod == 1) {
                $codGateway = \App\Models\PaymentGateway::where('slug', 'cash-on-delivery')->first();
                if ($codGateway && $codGateway->status == Activity::DISABLE) {
                    $validator->errors()->add('payment_method', 'Pay on Delivery option is currently disabled.');
                }
            }
        });
    }
}
