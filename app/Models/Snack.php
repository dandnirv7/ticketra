<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Snack extends Model
{
    
    protected $primaryKey = 'id';

    
    protected $keyType = 'string';

    
    public $incrementing = false;

    
    protected $fillable = [
        'id',
        'name',
        'desc',
        'price',
        'emoji',
        'category',
        'status',
        'popular',
        'layout',
    ];
}
