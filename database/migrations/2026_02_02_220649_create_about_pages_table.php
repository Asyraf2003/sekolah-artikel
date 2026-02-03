<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('about_sections', function (Blueprint $table) {
            $table->id();

            $table->longText('vision_content_html_id')->nullable();
            $table->longText('vision_content_html_en')->nullable();
            $table->longText('vision_content_html_ar')->nullable();

            $table->longText('vision_content_delta_id')->nullable();
            $table->longText('vision_content_delta_en')->nullable();
            $table->longText('vision_content_delta_ar')->nullable();

            $table->longText('mission_content_html_id')->nullable();
            $table->longText('mission_content_html_en')->nullable();
            $table->longText('mission_content_html_ar')->nullable();

            $table->longText('mission_content_delta_id')->nullable();
            $table->longText('mission_content_delta_en')->nullable();
            $table->longText('mission_content_delta_ar')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('about_sections');
    }
};
