<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SnackOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'snack_order_id',
        'snack_id',
        'snack_name',
        'snack_emoji',
        'qty',
        'price',
    ];

    public function snackOrder(): BelongsTo
    {
        return $this->belongsTo(SnackOrder::class);
    }
}
