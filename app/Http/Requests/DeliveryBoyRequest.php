<?php

namespace App\Http\Requests;

use App\Rules\ValidPhone;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DeliveryBoyRequest extends FormRequest
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
    public function rules()
    {
        $isExistingUser = !empty($this->existing_user_id);

        return [
            'existing_user_id'      => ['nullable', 'numeric', 'exists:users,id'],
            'name'                  => [$isExistingUser ? 'nullable' : 'required', 'string', 'max:190'],
            'email'                 => [
                $isExistingUser ? 'nullable' : 'required',
                'email',
                'max:190',
                Rule::unique("users", "email")->whereNull('deleted_at')->ignore($this->existing_user_id ?: $this->route('deliveryBoy.id'))
            ],
            'password'              => $isExistingUser ? ['nullable'] : [
                $this->route('deliveryBoy.id') ? 'nullable' : 'required',
                'string',
                'min:6',
                'confirmed'
            ],
            'password_confirmation' => $isExistingUser ? ['nullable'] : [
                $this->route('deliveryBoy.id') ? 'nullable' : 'required',
                'string',
                'min:6'
            ],
            'username'              => [
                'nullable',
                'max:190',
                Rule::unique("users", "username")->whereNull('deleted_at')->ignore($this->existing_user_id ?: $this->route('deliveryBoy.id'))
            ],
            'device_token'          => ['nullable', 'string'],
            'web_token'             => ['nullable', 'string'],
            'phone'                 => [
                'nullable',
                'string',
                'max:20',
                new ValidPhone(),
                Rule::unique("users", "phone")->whereNull('deleted_at')->ignore($this->existing_user_id ?: $this->route('deliveryBoy.id'))
            ],
            'branch_id'             => ['nullable', 'numeric'],
            'status'                => ['required', 'numeric', 'max:24'],
            'country_code'          => [$isExistingUser ? 'nullable' : 'required', 'string', 'max:20'],
        ];
    }
}
