<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketingStrategyItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'area_number',
        'area_name',
        'task',
        'cost',
        'status',
        'notes',
    ];
}
