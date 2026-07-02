<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketingReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'week_number',
        'reporting_period',
        'prepared_by',
        'department',
        'date_submitted',
        'reviewed_by',
        'tasks_data',
        'kpi_data',
        'social_media_data',
        'website_performance_data',
        'google_business_data',
        'bookings_leads_data',
        'paid_ads_data',
        'content_created_data',
        'challenges_data',
        'achievements_data',
        'next_week_plan_data',
    ];

    protected $casts = [
        'tasks_data' => 'array',
        'kpi_data' => 'array',
        'social_media_data' => 'array',
        'website_performance_data' => 'array',
        'google_business_data' => 'array',
        'bookings_leads_data' => 'array',
        'paid_ads_data' => 'array',
        'content_created_data' => 'array',
        'challenges_data' => 'array',
        'achievements_data' => 'array',
        'next_week_plan_data' => 'array',
        'date_submitted' => 'date',
    ];
}
