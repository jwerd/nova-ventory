<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function ($product) {
            $product->user_id    = auth()->user()->id;
            $product->company_id = auth()->user()->company_id;
        });
    }

    protected $fillable = [
        'name',
        'purchased_price',
        'list_price',
        'available',
        'sold',
    ];
}
