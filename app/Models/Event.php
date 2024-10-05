<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'banner',
        'start_date',
        'price',
        'seats',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'price' => 'float',
    ];
}
