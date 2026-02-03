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

            $table->string('title_id');
            $table->string('title_en')->nullable();
            $table->string('title_ar')->nullable();

            $table->string('slug')->unique();

            $table->string('hero_image')->nullable();

            $table->string('excerpt_id', 300)->nullable();
            $table->string('excerpt_en', 300)->nullable();
            $table->string('excerpt_ar', 300)->nullable();

            // Opsi 2: konten per bahasa
            $table->longText('content_delta_id')->nullable();
            $table->longText('content_delta_en')->nullable();
            $table->longText('content_delta_ar')->nullable();

            $table->longText('content_html_id')->nullable();
            $table->longText('content_html_en')->nullable();
            $table->longText('content_html_ar')->nullable();

            $table->enum('status', ['draft', 'published', 'archived'])
                ->default('draft')
                ->index();

            $table->timestamp('published_at')->nullable()->index();

            $table->boolean('is_featured')->default(false)->index();
            $table->timestamp('pinned_until')->nullable()->index();

            $table->unsignedBigInteger('view_count')->default(0)->index();
            $table->unsignedInteger('comment_count')->default(0)->index();
            $table->unsignedInteger('share_count')->default(0);
            $table->unsignedSmallInteger('reading_time')->default(0);

            $table->timestamps();
            $table->softDeletes();

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
