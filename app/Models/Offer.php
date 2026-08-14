<?php

namespace App\Models;

use App\Enums\DiscountType;
use Spatie\Image\Enums\CropPosition;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Offer extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $table = "offers";
    protected $fillable = ['name', 'description', 'slug', 'amount', 'discount_type', 'status', 'start_date', 'end_date', 'visible_days'];
    protected $casts = [
        'id'         => 'integer',
        'name'       => 'string',
        'description' => 'string',
        'slug'       => 'string',
        'amount'     => 'decimal:6',
        'discount_type' => 'integer',
        'status'     => 'integer',
        'start_date' => 'datetime',
        'end_date'   => 'datetime',
        'visible_days' => 'array',
    ];

    public function items(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'offer_items');
    }

    public function offerItems(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(OfferItem::class, 'offer_id', 'id');
    }

    public function getCoverAttribute(): string
    {
        if (!empty($this->getFirstMediaUrl('offer'))) {
            // Offers are promotional posters; preserve the full artwork instead
            // of cropping it into the old banner ratio.
            return $this->getFirstMediaUrl('offer');
        }
        return asset('images/default/offer.png');
    }

    public function discountedPrice(float $price): float
    {
        $discount = $this->discount_type === DiscountType::FIXED
            ? (float) $this->amount
            : ($price / 100 * (float) $this->amount);

        return max(0, $price - $discount);
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('cover')->crop(548, 140, CropPosition::Center)->keepOriginalImageFormat()->sharpen(10);
    }
}
