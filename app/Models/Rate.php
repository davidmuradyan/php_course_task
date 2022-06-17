<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rate extends Model
{
    use HasFactory;

    protected array $fillable = [
        'user_id',
        'order_id',
        'product_id',
        'rate',
        'comment'
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
