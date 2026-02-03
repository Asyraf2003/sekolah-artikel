<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();

            // Judul multi-bahasa
            $table->string('title_id');
            $table->string('title_en')->nullable();
            $table->string('title_ar')->nullable();

            // Deskripsi multi-bahasa
            $table->text('desc_id')->nullable();
            $table->text('desc_en')->nullable();
            $table->text('desc_ar')->nullable();

            // Tanggal pengumuman
            $table->date('event_date');

            // Link tujuan (opsional) - URL bisa panjang
            $table->string('link_url', 2048)->nullable();

            // Status publikasi
            $table->boolean('is_published')->default(true);
            $table->timestamp('published_at')->nullable();

            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();
            $table->softDeletes();

            // Index yang lebih “niat” (nggak dobel-dobel)
            $table->index(['event_date']);
            $table->index(['is_published', 'published_at']);
            $table->index(['is_published', 'sort_order', 'event_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
