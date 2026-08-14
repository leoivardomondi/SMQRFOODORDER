<?php

namespace App\Http\Resources;


use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class SimpleOfferListResource extends JsonResource
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
            'id'                 => $this->id,
            'name'               => $this->name,
            'description'        => $this->description,
            "slug"               => $this->slug,
            'amount'             => $this->amount === null ? 0 : $this->amount,
            'discount_type'     => $this->discount_type,
            "flat_amount"        => AppLibrary::flatAmountFormat($this->amount),
            "convert_amount"     => AppLibrary::convertAmountFormat($this->amount),
            'convert_start_date' => AppLibrary::datetime($this->start_date),
            'convert_end_date'   => AppLibrary::datetime($this->end_date),
            'start_date'         => $this->start_date,
            'end_date'           => $this->end_date,
            'visible_days'       => $this->visible_days ?? [],
            'status'             => $this->status,
            'image'              => $this->cover,
        ];
    }
}
