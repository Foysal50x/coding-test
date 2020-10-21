<?php
namespace App\QueryFilters;


use Faisal50x\QueryFilter\QueryFilter;
use Illuminate\Database\Eloquent\Builder;

class ProductFilter extends QueryFilter
{
    /**
     * @param Builder|\Illuminate\Database\Query\Builder $query
     * @param string $date
     * @return mixed
     */
    public function date($query, string $date = '')
    {
        $date = str_replace(" ", "", trim($date));
        if (empty($date)) {
            return $query;
        }
        return $query->whereDate('created_at', $date);
    }
    /**
     * @param Builder|\Illuminate\Database\Query\Builder $query
     * @param string $title
     * @return string|string[]
     */
    public function title($query, string $title = '')
    {
        $title = str_replace(" ", "", trim($title));
        if (empty($title)) {
            return $query;
        }
        return $query->where('title', "LIKE", "%$title%");
    }

    /**
     * @param Builder|\Illuminate\Database\Query\Builder $query
     * @param string $variant
     */
    public function variant($query, string $variant = "")
    {
        $variant = str_replace(' ', '', trim($variant));
        if (empty($variant)) {
            return $query;
        }
        return $query->whereHas('productVariants', function ($query) use($variant){
            $query->where("variant", "LIKE", "%$variant%");
        });
    }
    /**
     * @param Builder|\Illuminate\Database\Query\Builder $query
     * @param int $min
     * @param int $max
     */
    public function priceRange($query, $min = 0, $max = 0)
    {
        if (is_null($min) || is_null($max)) {
            return $query;
        }
        if ((int) $max <= 0) {
            return $query;
        }
        if ($min > $max) {
            return $query;
        }
        return $query->whereHas('priceVariants', function($query) use($min, $max){
            $query->whereBetween('price', [$min, $max]);
        });

    }
}
