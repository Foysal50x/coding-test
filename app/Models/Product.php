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

    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_id');
    }
    public function variants()
    {
        return $this->belongsToMany(Variant::class ,
            'product_variants',
            'product_id',
            'variant_id'
        )->withPivot('variant');
    }

    public function productVariants()
    {
        return $this->hasMany(ProductVariant::class, 'product_id');
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
