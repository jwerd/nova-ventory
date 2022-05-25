<?php

namespace App\Models;

use App\Models\Scopes\ItemScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Database\Eloquent\Casts\Attribute;


class Item extends Model implements HasMedia
{
    use HasFactory;
    use SoftDeletes;
    use InteractsWithMedia;

    protected $fillable = [
        'name',
        'description',
        'qty',
        'price',
        'list_price',
        'price_sold',
        'sold_on',
        'dimension',
        'available',
        'user_id',
    ];

    protected $casts = [
        'dimension' => 'array',
        'sold_on'   => 'datetime'
    ];

    protected static function booted()
    {
        static::addGlobalScope(new ItemScope);
//        static::addGlobalScope(new UnsoldItemsScope);

        static::creating(function ($item) {
            // Set our defaults for old system
            $item->qty = 1;
            $item->available = true;
            $item->user_id = auth()->id() ?? 1;
        });
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this
            ->addMediaConversion('thumb')
            ->width(200)
            ->height(200);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('main')->singleFile();
    }

    public function getSoldOnAttribute($value)
    {
        // Return the sold_on if set
        return $value ?? $this->updated_at;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function markItemSold(float $price): void
    {
        $merged = [];

        // Set list price if we don't already have it.  It should be the same as the price sold.
        if($this->list_price == '0.0' || blank($this->list_price)) {
            $merged = [
                'list_price' => $price
            ];
        }

        $this->update(array_merge($merged, [
            'price_sold' => $price,
            'sold_on'    => now(),
            'available'  => false
        ]));
    }

    /**
     * Scope a query to only include sold items.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return void
     */
    public function scopeSold($query)
    {
        $query->where('available', false)
            ->whereNotNull('price_sold');
    }

    /**
     * Get the item's total revenue
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function revenue(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                $price      = data_get($attributes, 'price', 0);
                $sold_price = data_get($attributes, 'price_sold', 0);

                return ($sold_price > $price) ? $sold_price-$price : 0;
            }
        );
    }
}
