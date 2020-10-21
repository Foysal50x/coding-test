<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $fillable = [
        'title', 'sku', 'description'
    ];

    protected $appends = ['excerpt'];

    public function variants()
    {
        return $this->belongsToMany(Variant::class ,
            'product_variants',
            'product_id',
            'variant_id'
        )->withPivot('variant');
    }



    public function priceVariants()
    {
        return $this->hasMany(ProductVariantPrice::class);
    }


    public function getExcerptAttribute()
    {
        return Str::words($this->description, 25);
    }
}
