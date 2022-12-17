<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;
    public $table = 'categories';

    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function user_category(): HasMany
    {
        return $this->hasMany(UserCategory::class, 'category_id', 'id');
    }

    public function categories()
    {
        return $this->belongsToMany(OrderUnitCategory::class,
            'order_unit_has_categories',
            'order_unit_id',
            'order_unit_category_id');
    }

    public function order()
    {
        return $this->hasOneThrough(Order::class, OrderUnit::class, 'id', 'id', 'order_unit_id', 'order_id');
    }

}
