<?php

namespace App\Http\Requests;

use App\Rules\IniAmount;
use App\Enums\DiscountType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OfferRequest extends FormRequest
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
            'name'        => [
                'required',
                'string',
                'max:190',
                Rule::unique("offers", "name")->ignore($this->route('offer'))
            ],
            'description' => ['nullable', 'string', 'max:2000'],
            'amount'     => ['required', 'numeric', new IniAmount()],
            'discount_type' => ['required', 'integer', Rule::in([DiscountType::FIXED, DiscountType::PERCENTAGE])],
            'status'     => ['required', 'numeric', 'max:24'],
            'start_date' => ['required', 'string', 'max:190'],
            'end_date'   => ['required', 'string', 'max:190'],
            'visible_days' => ['nullable', 'array'],
            'visible_days.*' => ['string', 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday'],
            'image'      => $this->route('offer.id') ? ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif,heic,heif', 'max:51200'] : ['required', 'image', 'mimes:jpg,jpeg,png,webp,gif,heic,heif', 'max:51200'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (!$this->isNotNull(request('start_date'))) {
                $validator->errors()->add('start_date', 'The start date field is required');
            }

            if (!$this->isNotNull(request('end_date'))) {
                $validator->errors()->add('end_date', 'The end date field is required');
            }
            if ((int) request('discount_type') === DiscountType::PERCENTAGE && (float) request('amount') > 100) {
                $validator->errors()->add('amount', 'Percentage discount cannot exceed 100.');
            }

            if ($this->isNotNull(request('start_date')) && strtotime(request('end_date')) < strtotime(request('start_date'))) {
                $validator->errors()->add('end_date', 'To date can\'t be older than Start date.');
            }
            if ($this->isNotNull(request('start_date')) && $this->checkToDate()) {
                $validator->errors()->add('end_date', 'To date can\'t be older than now.');
            }
        });
    }


    private function checkToDate()
    {
        $today = strtotime(date('Y-m-d H:i:s'));
        if (strtotime(request('end_date')) < $today) {
            return true;
        }
    }

    private function isNotNull($value)
    {
        if ($value === 'null') {
            return false;
        }
        return true;
    }
}
