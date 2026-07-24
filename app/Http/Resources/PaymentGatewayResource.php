<?php

namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;

class PaymentGatewayResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */

    public function toArray($request) : array
    {
        $primaryGateway = \Smartisan\Settings\Facades\Settings::group('payment_gateway')->get('primary_payment_gateway', 'pesapal');

        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'slug'       => $this->slug,
            'status'     => $this->status,
            'is_primary' => $primaryGateway === $this->slug,
            'options'    => $this->gatewayOptions ? GatewayOptionsResource::collection($this->gatewayOptions) : []
        ];
    }

}
