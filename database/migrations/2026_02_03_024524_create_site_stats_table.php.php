<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('site_stats', function (Blueprint $table) {
            $table->id();

            // Slot posisi: 1..4 (bisa kamu extend jadi 1..6 nanti)
            $table->unsignedTinyInteger('slot')->unique();

            // Angka statistik (disimpan normal, format Arab di view)
            $table->unsignedInteger('value')->default(0);

            // Label multi-bahasa (opsional tapi berguna)
            $table->string('label_id')->nullable();
            $table->string('label_en')->nullable();
            $table->string('label_ar')->nullable();

            // Deskripsi multi-bahasa
            $table->string('desc_id')->nullable();
            $table->string('desc_en')->nullable();
            $table->string('desc_ar')->nullable();

            // Bisa hide/show tanpa hapus
            $table->boolean('is_active')->default(true)->index();

            // Kalau suatu saat mau re-order
            $table->unsignedTinyInteger('sort_order')->default(0)->index();

            $table->timestamps();

            // Index untuk pengambilan public: active + order
            $table->index(['is_active', 'sort_order', 'slot']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_stats');
    }
};
