<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marketing_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('subject');
            $table->string('headline');
            $table->string('banner_url', 500)->nullable();
            $table->text('content');
            $table->string('cta_text')->nullable();
            $table->string('cta_url', 500)->nullable();
            $table->string('discount_code')->nullable();
            $table->string('target_audience');
            $table->integer('recipients_count')->default(0);
            $table->string('sent_by');
            $table->string('status')->default('Sent');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketing_campaigns');
    }
};
