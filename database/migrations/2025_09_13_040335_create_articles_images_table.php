<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('author_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Multibahasa masih pakai kolom (biar kamu ga bongkar semua sekaligus).
            $table->string('title_id');
            $table->string('title_en')->nullable();
            $table->string('title_ar')->nullable();

            // Slug auto di model, tapi DB tetap enforce unique.
            $table->string('slug')->unique();

            $table->string('hero_image')->nullable();

            // Excerpt masih kepake buat listing/SEO ringan. Meta_* dibuang total.
            $table->string('excerpt_id', 300)->nullable();
            $table->string('excerpt_en', 300)->nullable();
            $table->string('excerpt_ar', 300)->nullable();

            /**
             * Quill content:
             * - content_delta: simpan Delta JSON (buat edit round-trip).
             * - content_html: optional cache hasil render HTML (buat tampil cepat).
             */
            $table->json('content_delta')->nullable();
            $table->longText('content_html')->nullable();

            /**
             * Status simpel:
             * - draft: belum tayang
             * - published: tayang (kalau published_at future => otomatis "scheduled" secara implisit)
             * - archived: disembunyikan
             */
            $table->enum('status', ['draft', 'published', 'archived'])
                ->default('draft')
                ->index();

            $table->timestamp('published_at')->nullable()->index();

            // Optional fitur editorial yang masuk akal (bukan redundan)
            $table->boolean('is_featured')->default(false)->index();
            $table->timestamp('pinned_until')->nullable()->index();

            // Counter cached (boleh, ga ganggu)
            $table->unsignedBigInteger('view_count')->default(0)->index();
            $table->unsignedInteger('comment_count')->default(0)->index();
            $table->unsignedInteger('share_count')->default(0);
            $table->unsignedSmallInteger('reading_time')->default(0);

            $table->timestamps();
            $table->softDeletes();

            // Fulltext buat search cepat (MySQL InnoDB modern OK)
            $table->fullText(
                ['title_id','title_en','title_ar','slug','excerpt_id','excerpt_en','excerpt_ar'],
                'ft_articles_search'
            );

            $table->index(['status','published_at'], 'idx_articles_status_published_at');
            $table->index(['is_featured','published_at'], 'idx_articles_featured_published_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
