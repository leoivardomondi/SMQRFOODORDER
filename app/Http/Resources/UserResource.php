<?php

namespace App\Http\Resources;


use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            "id"               => $this->id,
            "name"             => $this->name,
            "first_name"       => $this->FirstName,
            "last_name"        => $this->LastName,
            "phone"            => $this->phone,
            "email"            => $this->email,
            'username'         => $this->username,
            "balance"          => AppLibrary::flatAmountFormat($this->balance),
            "currency_balance" => AppLibrary::currencyAmountFormat($this->balance),
            "image"            => $this->image,
            "role_id"          => $this->myRole,
            "country_code"     => $this->country_code,
            "order"            => $this->orders->count(),
            "trust_metrics"    => app(\App\Services\TrustScoreService::class)->getUserMetrics($this->resource),
            "can_pay_on_delivery" => app(\App\Services\TrustScoreService::class)->canPayOnDelivery($this->resource),
            'create_date'      => AppLibrary::date($this->created_at),
            'update_date'      => AppLibrary::date($this->updated_at),

        ];
    }
}
