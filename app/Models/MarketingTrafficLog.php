<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketingTrafficLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'ip_address',
        'url',
        'page_type',
        'referrer',
        'source',
        'device_type',
        'event_type',
        'utm_source',
        'utm_medium',
        'utm_campaign',
    ];
}
