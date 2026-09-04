<?php

namespace App\Http\Resources;


use App\Enums\Status;
use App\Libraries\AppLibrary;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class NormalItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $price = $this->price;
        $compareAtPrice = (float) ($this->compare_at_price ?? 0);
        $hasGlovoComparison = $compareAtPrice > (float) $price;
        return [
            "id"             => $this->id,
            "name"           => $this->name,
            "slug"           => $this->slug,
            "branch_id"      => (int) ($this->branch_id ?? 0),
            "branch_name"    => $this->branch ? $this->branch->name : 'All Branches',
            "flat_price"     => AppLibrary::flatAmountFormat($this->price),
            "convert_price"  => AppLibrary::convertAmountFormat($this->price),
            "currency_price" => AppLibrary::currencyAmountFormat($this->price),
            "price"          => $this->price,
            "compare_at_price" => $hasGlovoComparison ? $compareAtPrice : null,
            "compare_at_currency_price" => $hasGlovoComparison ? AppLibrary::currencyAmountFormat($compareAtPrice) : null,
            "has_glovo_comparison" => $hasGlovoComparison,
            "glovo_comparison_discount_percentage" => $hasGlovoComparison ? (int) round((($compareAtPrice - (float) $price) / $compareAtPrice) * 100) : 0,
            "item_type"      => $this->item_type,
            "status"         => $this->status,
            "description"    => $this->description === null ? '' : $this->description,
            "caution"        => $this->caution === null ? '' : $this->caution,
            "thumb"          => $this->thumb,
            "cover"          => $this->cover,
            "preview"        => $this->preview,
            "variations"     => $this->variations->groupBy('item_attribute_id'),
            "itemAttributes" => ItemAttributeResource::collection($this->itemAttributeList($this->variations)),
            "extras"         => ItemExtraResource::collection($this->extras->load('item')),
            "addons"         => ItemAddonResource::collection($this->addons->load('addonItem', 'addonItem.variations','addonItem.offer', 'item')),
            "offer"          => SimpleOfferResource::collection(
                $this->offer->filter(function ($offer) use ($price) {
                    if (Carbon::now()->between(
                            $offer->start_date,
                            $offer->end_date
                        ) && $offer->status === Status::ACTIVE) {
                        $offer->flat_price     = AppLibrary::flatAmountFormat($offer->discountedPrice($price));
                        $offer->convert_price  = AppLibrary::convertAmountFormat(
                            $offer->discountedPrice($price)
                        );
                        $offer->currency_price = AppLibrary::currencyAmountFormat(
                            $offer->discountedPrice($price)
                        );
                        return $offer;
                    }
                })
            )
        ];
    }

    private function itemAttributeList($variations
    ) : \Vanilla\Support\Collection | \IlluminateAgnostic\Str\Support\Collection | \IlluminateAgnostic\StrAgnostic\Str\Support\Collection | \IlluminateAgnostic\Collection\Support\Collection | \IlluminateAgnostic\ArrAgnostic\Arr\Support\Collection | \Illuminate\Support\Collection | \IlluminateAgnostic\Arr\Support\Collection {
        $array = [];
        foreach ($variations as $b) {
            if (!isset($array[$b->itemAttribute->id])) {
                $array[$b->itemAttribute->id] = (object)[
                    'id'     => $b->itemAttribute->id,
                    'name'   => $b->itemAttribute->name,
                    'status' => $b->itemAttribute->status
                ];
            }
        }
        return collect($array);
    }
}
