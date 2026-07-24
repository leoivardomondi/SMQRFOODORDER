<?php

namespace App\Http\Resources;


use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
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
            'id'              => $this->id,
            'order_id'        => $this->order_id,
            'order_serial_no' => $this->order?->order_serial_no,
            'transaction_no'  => $this->transaction_no,
            'provider_receipt' => $this->provider_receipt,
            'provider_name'    => $this->provider_name,
            'payer_phone'      => $this->payer_phone,
            'payer_phone_last4' => $this->payer_phone_last4,
            'payment_channel'  => $this->payment_channel,
            'amount'          => AppLibrary::flatAmountFormat($this->amount),
            'payment_method'  => strtoupper($this->payment_method),
            'type'            => $this->type,
            'sign'            => $this->sign,
            'date'            => AppLibrary::datetime($this->created_at),
            'payment_date'    => AppLibrary::date($this->created_at, 'd-m-Y'),
            'payment_time'    => AppLibrary::time($this->created_at, 'h:i A'),
        ];
    }
}
