<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Variant extends Model
{
    protected $fillable = [
        'title', 'description'
    ];

    public function variants()
    {
        return $this->belongsToMany(Product::class,
            'product_variants',
            'variant_id',
            'product_id'
        );
    }
}
