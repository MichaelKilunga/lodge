<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('marketing_traffic_logs', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->index();
            $table->string('ip_address')->nullable();
            $table->string('url')->nullable();
            $table->string('page_type')->default('Home');
            $table->string('referrer', 500)->nullable();
            $table->string('source')->default('Direct'); // Facebook, Instagram, TikTok, Google Search, Google Business Profile, WhatsApp, Direct, etc.
            $table->string('device_type')->default('Desktop'); // Mobile, Desktop, Tablet
            $table->string('event_type')->default('page_view'); // page_view, whatsapp_click, phone_click, contact_form_submit, booking_click, booking_completed
            $table->string('utm_source')->nullable();
            $table->string('utm_medium')->nullable();
            $table->string('utm_campaign')->nullable();
            $table->timestamps();
        });

        Schema::create('marketing_reports', function (Blueprint $table) {
            $table->id();
            $table->string('week_number')->nullable();
            $table->string('reporting_period')->nullable();
            $table->string('prepared_by')->nullable();
            $table->string('department')->default('MEDIA & ICT');
            $table->date('date_submitted')->nullable();
            $table->string('reviewed_by')->nullable();
            $table->longText('tasks_data')->nullable();
            $table->longText('kpi_data')->nullable();
            $table->longText('social_media_data')->nullable();
            $table->longText('website_performance_data')->nullable();
            $table->longText('google_business_data')->nullable();
            $table->longText('bookings_leads_data')->nullable();
            $table->longText('paid_ads_data')->nullable();
            $table->longText('content_created_data')->nullable();
            $table->longText('challenges_data')->nullable();
            $table->longText('achievements_data')->nullable();
            $table->longText('next_week_plan_data')->nullable();
            $table->timestamps();
        });

        Schema::create('marketing_strategy_items', function (Blueprint $table) {
            $table->id();
            $table->integer('area_number');
            $table->string('area_name');
            $table->string('task');
            $table->string('cost')->nullable();
            $table->string('status')->default('Not Started');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketing_strategy_items');
        Schema::dropIfExists('marketing_reports');
        Schema::dropIfExists('marketing_traffic_logs');
    }
};

