<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Orders extends Model
{
    use HasFactory;
    public $table = 'orders';

    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'purchaser_id', 'id');
    }

    public function orderItem(): HasMany
    {
        return $this->hasMany(OrderItems::class, 'order_id', 'id');
    }

    public function userCategory()
    {
        return $this->hasOne(UserCategory::class, 'user_id', 'purchaser_id');
    }
}
