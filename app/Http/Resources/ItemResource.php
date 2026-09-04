<?php

namespace App\Http\Resources;


use App\Enums\Status;
use App\Libraries\AppLibrary;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request): array
    {
        $price = $this->price;
        $compareAtPrice = (float) ($this->compare_at_price ?? 0);
        $hasGlovoComparison = $compareAtPrice > (float) $price;
        return [
            "id"               => $this->id,
            "name"             => $this->name,
            "slug"             => $this->slug,
            "item_category_id" => $this->item_category_id,
            "branch_id"        => (int) ($this->branch_id ?? 0),
            "branch_name"      => $this->branch ? $this->branch->name : 'All Branches',
            "tax_id"           => $this->tax_id,
            "flat_price"       => AppLibrary::flatAmountFormat($this->price),
            "convert_price"    => AppLibrary::convertAmountFormat($this->price),
            "currency_price"   => AppLibrary::currencyAmountFormat($this->price),
            "price"            => $this->price,
            "compare_at_price" => $hasGlovoComparison ? $compareAtPrice : null,
            "compare_at_currency_price" => $hasGlovoComparison ? AppLibrary::currencyAmountFormat($compareAtPrice) : null,
            "has_glovo_comparison" => $hasGlovoComparison,
            "glovo_comparison_discount_percentage" => $hasGlovoComparison ? (int) round((($compareAtPrice - (float) $price) / $compareAtPrice) * 100) : 0,
            "item_type"        => $this->item_type,
            "is_featured"      => $this->is_featured,
            "status"           => $this->status,
            "description"      => $this->description === null ? '' : $this->description,
            "visible_days"     => $this->visible_days ?? [],
            "is_daily_offer"   => $this->is_daily_offer,
            "caution"          => $this->caution === null ? '' : $this->caution,
            "order"            => $this->orders->count(),
            "thumb"            => $this->thumb,
            "cover"            => $this->cover,
            "preview"          => $this->preview,
            "category_name"    => optional($this->category)->name,
            "category"         => new ItemCategoryResource($this->category),
            "tax"              => new TaxResource($this->tax),
            "variations"       => $this->variations->groupBy('item_attribute_id'),
            "itemAttributes"   => ItemAttributeResource::collection($this->itemAttributeList($this->variations)),
            "extras"           => ItemExtraResource::collection($this->extras),
            "addons"           => ItemAddonResource::collection($this->resolveDefaultAddons()),
            "offer"            => SimpleOfferResource::collection(
                $this->offer->filter(function ($offer) use ($price) {
                    if (Carbon::now()->between($offer->start_date, $offer->end_date) && $offer->status === Status::ACTIVE) {
                        $amount                = $offer->discountedPrice($price);
                        $offer->flat_price     = AppLibrary::flatAmountFormat($amount);
                        $offer->convert_price  = AppLibrary::convertAmountFormat($amount);
                        $offer->currency_price = AppLibrary::currencyAmountFormat($amount);
                        return $offer;
                    }
                })
            )
        ];
    }

    private function resolveDefaultAddons()
    {
        $addonsList = $this->addons->load('addonItem');
        
        $categoryName = strtoupper(optional($this->category)->name ?? '');
        $isExcluded = str_contains($categoryName, 'OFFER') || 
                     str_contains($categoryName, 'GROUP DEAL') || 
                     str_contains($categoryName, 'JUICE') || 
                     str_contains($categoryName, 'COLD-PRESSED');

        if (!$isExcluded) {
            $juiceCategory = \App\Models\ItemCategory::where('name', 'LIKE', '%COLD-PRESSED%')
                ->orWhere('name', 'LIKE', '%JUICE%')
                ->first();
            if ($juiceCategory) {
                $juiceItems = \App\Models\Item::where('item_category_id', $juiceCategory->id)
                    ->where('status', Status::ACTIVE)
                    ->get();

                $existingAddonItemIds = $addonsList->pluck('addon_item_id')->filter()->toArray();

                foreach ($juiceItems as $juiceItem) {
                    if (!in_array($juiceItem->id, $existingAddonItemIds) && $juiceItem->id !== $this->id) {
                        $syntheticAddon = new \App\Models\ItemAddon([
                            'item_id' => $this->id,
                            'addon_item_id' => $juiceItem->id,
                            'addon_item_variation' => null,
                        ]);
                        $syntheticAddon->setRelation('addonItem', $juiceItem);
                        $addonsList->push($syntheticAddon);
                    }
                }
            }
        }

        return $addonsList;
    }

    private function itemAttributeList($variations)
    {
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
