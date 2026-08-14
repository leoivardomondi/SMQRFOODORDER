<?php

namespace App\Http\Resources;


use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemVariationResource extends JsonResource
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
            'id'                => $this->id,
            'item_id'           => $this->item_id,
            'linked_item_id'    => $this->linked_item_id,
            'item_attribute_id' => $this->item_attribute_id,
            'name'              => $this->name,
            'price'             => $this->price,
            "flat_price"        => AppLibrary::flatAmountFormat($this->price),
            "convert_price"     => AppLibrary::convertAmountFormat($this->price),
            "currency_price"    => AppLibrary::currencyAmountFormat($this->price),
            'caution'           => $this->caution,
            'status'            => $this->status,
            'item'              => optional($this->item)->name,
            'linked_item'       => optional($this->linkedItem)->name,
            'item_attribute'    => optional($this->itemAttribute)->name,
        ];
    }
}
