<?php

namespace App\Models;

use App\Models\Scopes\ItemScope;
use App\Models\Scopes\SoldItemsScope;
use App\Models\Scopes\UnsoldItemsScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Database\Eloquent\Casts\Attribute;

class SoldItem extends Model implements HasMedia
{
    use HasFactory;
    use SoftDeletes;
    use InteractsWithMedia;

    protected $table = 'items';

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
        static::addGlobalScope(new SoldItemsScope);
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
