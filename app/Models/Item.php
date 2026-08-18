<?php

namespace App\Models;

use Carbon\Carbon;
use App\Enums\Status;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Item extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, SoftDeletes;

    protected $table = "items";
    protected $fillable = [
        'name',
        'item_category_id',
        'slug',
        'tax_id',
        'item_type',
        'price',
        'is_featured',
        'description',
        'visible_days',
        'caution',
        'status',
        'order',
    ];
    protected $dates = ['deleted_at'];
    protected $casts = [
        'id'               => 'integer',
        'name'             => 'string',
        'item_category_id' => 'integer',
        'slug'             => 'string',
        'tax_id'           => 'integer',
        'item_type'        => 'integer',
        'price'            => 'decimal:6',
        'is_featured'      => 'integer',
        'description'      => 'string',
        'visible_days'     => 'array',
        'caution'          => 'string',
        'status'           => 'integer',
        'order'            => 'integer',
    ];

    public function getThumbAttribute(): string
    {
        if (!empty($this->getFirstMediaUrl('item'))) {
            $item = $this->getMedia('item')->last();
            return $item->getUrl('thumb');
        }
        return asset('images/item/thumb.png');
    }

    public function getCoverAttribute(): string
    {
        if (!empty($this->getFirstMediaUrl('item'))) {
            $item = $this->getMedia('item')->last();
            if (str_contains(strtoupper(optional($this->category)->name ?? ''), 'DAILY OFFER')) {
                return $item->getUrl();
            }
            return $item->getUrl('cover');
        }
        return asset('images/item/cover.png');
    }

    public function getIsDailyOfferAttribute(): bool
    {
        return str_contains(strtoupper(optional($this->category)->name ?? ''), 'DAILY OFFER');
    }

    public function scopeVisibleToday(Builder $query): Builder
    {
        $today = strtolower(Carbon::now()->format('l'));

        return $query->where(function (Builder $days) use ($today): void {
            $days->whereNull('visible_days')
                ->orWhereJsonContains('visible_days', $today);
        });
    }

    public function isVisibleToday(?Carbon $date = null): bool
    {
        $days = array_map('strtolower', $this->visible_days ?? []);

        return empty($days) || in_array(strtolower(($date ?? Carbon::now())->format('l')), $days, true);
    }

    public function getPreviewAttribute(): string
    {
        if (!empty($this->getFirstMediaUrl('item'))) {
            $item = $this->getMedia('item')->last();
            return $item->getUrl('preview');
        }
        return asset('images/item/cover.png');
    }

    public function registerMediaConversions(Media $media = null): void
    {
        
        $this->addMediaConversion('thumb')->fit(Fit::Max, 336, 360)->keepOriginalImageFormat()->sharpen(10);
        $this->addMediaConversion('cover')->fit(Fit::Max, 1200, 900)->keepOriginalImageFormat()->sharpen(10);
        $this->addMediaConversion('preview')->width(600)->keepOriginalImageFormat()->sharpen(10);
    }

    public function variations(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ItemVariation::class)->with('itemAttribute')->where(['status' => Status::ACTIVE]);
    }

    public function extras(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ItemExtra::class)->where(['status' => Status::ACTIVE]);
    }

    public function addons(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ItemAddon::class);
    }

    public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ItemCategory::class, 'item_category_id', 'id');
    }

    public function tax(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Tax::class);
    }

    public function orders(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(OrderItem::class, 'item_id', 'id');
    }

    public function offer(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Offer::class, 'offer_items');
    }
}
