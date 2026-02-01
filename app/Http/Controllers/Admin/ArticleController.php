<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Article, Category, Tag};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $q       = trim((string)$request->get('q', ''));
        $sort    = (string)$request->get('sort', 'published_at_desc');
        $status  = $request->get('status');    // draft|published|archived|scheduled|null (scheduled = implicit)
        $catSlug = $request->get('category');
        $tagSlug = $request->get('tag');
        $feature = $request->get('featured');  // yes
        $hot     = $request->get('hot');       // legacy param -> kita map ke "pinned"

        $articles = Article::query()
            ->with(['author','categories','tags'])
            ->when($q !== '', fn($qq) => $qq->searchTitle($q))
            ->when($status, function ($qq) use ($status) {
                if ($status === 'scheduled') return $qq->scheduled();
                return $qq->where('status', $status);
            })
            ->when($feature === 'yes', fn($qq) => $qq->where('is_featured', true))
            // legacy "hot=yes" kita jadikan "pinned"
            ->when($hot === 'yes', function ($qq) {
                $qq->where(function($q){
                    $q->whereNotNull('pinned_until')
                      ->where('pinned_until', '>=', now());
                });
            })
            ->when($catSlug, fn($qq) => $qq->whereHas('categories', fn($c) => $c->where('slug', $catSlug)))
            ->when($tagSlug, fn($qq) => $qq->whereHas('tags', fn($t) => $t->where('slug', $tagSlug)))

            ->when($sort === 'published_at_asc', fn($qq) => $qq->orderBy('published_at','asc'))
            ->when($sort === 'title_asc',        fn($qq) => $qq->orderBy('title_id','asc'))
            ->when($sort === 'title_desc',       fn($qq) => $qq->orderBy('title_id','desc'))
            ->when($sort === 'views_desc',       fn($qq) => $qq->orderBy('view_count','desc'))
            ->when($sort === 'published_at_desc',fn($qq) => $qq->orderBy('published_at','desc'))
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        return view('admin.articles.index', compact('articles'));
    }

    public function create()
    {
        $categories = Category::active()->ordered()->get();
        $tags = Tag::popular()->get();

        return view('admin.articles.create', compact('categories','tags'));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $validated = $request->validate($this->articleRules());

        [$normalizedStatus, $normalizedPublishedAt] = $this->normalizeStatus(
            (string)($data['status'] ?? 'draft'),
            $data['published_at'] ?? null
        );

        DB::beginTransaction();
        try {
            $heroPath = null;
            if ($request->hasFile('hero_image')) {
                $heroPath = $request->file('hero_image')->store('articles/hero', 'public');
            }

            $deltaId = json_decode($validated['content_delta_id'], true);
            $deltaEn = filled($validated['content_delta_en'] ?? null) ? json_decode($validated['content_delta_en'], true) : null;
            $deltaAr = filled($validated['content_delta_ar'] ?? null) ? json_decode($validated['content_delta_ar'], true) : null;

            $article = Article::create([
                'author_id'  => Auth::id(),

                'title_id'   => trim((string)$validated['title_id']),
                'title_en'   => $validated['title_en'] ?? null,
                'title_ar'   => $validated['title_ar'] ?? null,

                'slug'       => null,
                'hero_image' => $heroPath,

                'excerpt_id' => $validated['excerpt_id'] ?? null,
                'excerpt_en' => $validated['excerpt_en'] ?? null,
                'excerpt_ar' => $validated['excerpt_ar'] ?? null,

                'content_delta_id' => $deltaId,
                'content_delta_en' => $deltaEn,
                'content_delta_ar' => $deltaAr,

                'content_html_id' => $validated['content_html_id'],
                'content_html_en' => $validated['content_html_en'] ?? null,
                'content_html_ar' => $validated['content_html_ar'] ?? null,

                'status'       => $normalizedStatus,
                'published_at' => $normalizedPublishedAt,

                'is_featured'  => (bool)($validated['is_featured'] ?? false),
                'pinned_until' => $validated['pinned_until'] ?? null,
            ]);

            $this->syncCategories($article, $data['category_ids'] ?? []);
            $this->syncTags($article, $data['tag_slugs'] ?? [], $data['tag_ids'] ?? []);

            $article->reading_time = $this->computeReadingMinutes($article->content_delta_id, $article->content_html_id);

            $article->save();

            DB::commit();
            return redirect()->route('admin.articles.index')->with('success', 'Artikel dibuat.');
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            return back()->withErrors('Gagal menyimpan artikel: '.$e->getMessage())->withInput();
        }
    }

    public function show(Article $article)
    {
        $article->load(['author','categories','tags']);
        return view('admin.article', compact('article'));
    }

    public function edit(Article $article)
    {
        $article->load(['categories','tags']);
        $categories = Category::active()->ordered()->get();
        $tags = Tag::popular()->get();

        return view('admin.articles.edit', compact('article','categories','tags'));
    }

    public function update(Request $request, Article $article)
    {
        $data = $request->all();
        $validated = $request->validate($this->articleRules());

        [$normalizedStatus, $normalizedPublishedAt] = $this->normalizeStatus(
            (string)($data['status'] ?? $article->status),
            $data['published_at'] ?? $article->published_at
        );

        DB::beginTransaction();
        try {
            $payload = [
                'title_id'   => trim((string)$validated['title_id']),
                'title_en'   => $validated['title_en'] ?? null,
                'title_ar'   => $validated['title_ar'] ?? null,

                'excerpt_id' => $validated['excerpt_id'] ?? null,
                'excerpt_en' => $validated['excerpt_en'] ?? null,
                'excerpt_ar' => $validated['excerpt_ar'] ?? null,

                'content_delta_id' => $deltaId,
                'content_delta_en' => $deltaEn,
                'content_delta_ar' => $deltaAr,

                'content_html_id' => $validated['content_html_id'],
                'content_html_en' => $validated['content_html_en'] ?? null,
                'content_html_ar' => $validated['content_html_ar'] ?? null,

                'status'       => $normalizedStatus,
                'published_at' => $normalizedPublishedAt,

                'is_featured'  => (bool)($validated['is_featured'] ?? false),
                'pinned_until' => $validated['pinned_until'] ?? null,
            ];

            $article->update($payload);

            if ($request->hasFile('hero_image')) {
                if ($article->hero_image && Storage::disk('public')->exists($article->hero_image)) {
                    Storage::disk('public')->delete($article->hero_image);
                }
                $article->hero_image = $request->file('hero_image')->store('articles/hero', 'public');
                $article->save();
            }

            $this->syncCategories($article, $data['category_ids'] ?? []);
            $this->syncTags($article, $data['tag_slugs'] ?? [], $data['tag_ids'] ?? []);

            $article->reading_time = $this->computeReadingMinutes($article->content_delta_id, $article->content_html_id);

            $article->save();

            DB::commit();
            return redirect()->route('admin.articles.index')->with('success', 'Artikel diperbarui.');
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            return back()->withErrors('Gagal memperbarui artikel: '.$e->getMessage())->withInput();
        }
    }

    public function destroy(Article $article)
    {
        DB::beginTransaction();
        try {
            if ($article->hero_image && Storage::disk('public')->exists($article->hero_image)) {
                Storage::disk('public')->delete($article->hero_image);
            }

            $article->categories()->detach();
            $article->tags()->detach();
            $article->delete();

            DB::commit();
            return redirect()->route('admin.articles.index')->with('success', 'Artikel dihapus.');
        } catch (\Throwable $e) {
            DB::rollBack(); report($e);
            return back()->withErrors('Gagal menghapus artikel: ' . $e->getMessage());
        }
    }

    /** ===== Helpers ===== */

    private function decodeDelta($value): ?array
    {
        if (is_array($value)) return $value;
        if (!is_string($value) || trim($value) === '') return null;

        $decoded = json_decode($value, true);
        return json_last_error() === JSON_ERROR_NONE ? $decoded : null;
    }

    private function syncCategories(Article $article, array $categoryIds): void
    {
        $ids = collect($categoryIds)->filter()->map(fn($v)=>(int)$v)->unique()->values()->all();
        $article->categories()->sync($ids);
    }

    private function syncTags(Article $article, array $tagSlugs = [], array $tagIds = []): void
    {
        $ids = collect($tagIds)->filter()->map(fn($v)=>(int)$v)->all();
        $slugs = collect($tagSlugs)->filter()->map(fn($v)=>Str::slug($v))->filter()->values()->all();

        if (!empty($slugs)) {
            $existing = Tag::whereIn('slug', $slugs)->get()->keyBy('slug');

            foreach ($slugs as $slug) {
                if (!isset($existing[$slug])) {
                    $tag = Tag::create(['name' => $slug, 'slug' => $slug]);
                    $ids[] = $tag->id;
                } else {
                    $ids[] = $existing[$slug]->id;
                }
            }
        }

        $ids = array_values(array_unique(array_filter($ids)));
        $article->tags()->sync($ids);

        // refresh use_count biar konsisten (nggak nambah terus tiap update)
        $this->refreshTagUseCount();
    }

    private function refreshTagUseCount(): void
    {
        $rows = DB::table('article_tag')
            ->select('tag_id', DB::raw('COUNT(*) as c'))
            ->groupBy('tag_id')
            ->pluck('c', 'tag_id');

        foreach ($rows as $tagId => $count) {
            DB::table('tags')->where('id', $tagId)->update(['use_count' => $count]);
        }
    }

    private function articleRules(): array
    {
        return [
            'title_id' => ['required','string','max:255'],
            'title_en' => ['nullable','string','max:255'],
            'title_ar' => ['nullable','string','max:255'],

            'excerpt_id' => ['nullable','string','max:300'],
            'excerpt_en' => ['nullable','string','max:300'],
            'excerpt_ar' => ['nullable','string','max:300'],

            'content_delta_id' => ['required','string','json'],
            'content_html_id'  => ['required','string'],

            'content_delta_en' => ['nullable','string','json'],
            'content_html_en'  => ['nullable','string'],

            'content_delta_ar' => ['nullable','string','json'],
            'content_html_ar'  => ['nullable','string'],

            'status' => ['required','in:draft,published,archived,scheduled'],
            'published_at' => ['nullable','date'],

            'is_featured' => ['nullable','boolean'],
            'pinned_until' => ['nullable','date'],

            'hero_image' => ['nullable','image','max:4096'],

            'category_ids' => ['array'],
            'category_ids.*' => ['integer'],
            'tag_ids' => ['array'],
            'tag_ids.*' => ['integer'],
            'tag_slugs' => ['array'],
            'tag_slugs.*' => ['string','max:64'],
        ];
    }

    private function normalizeStatus(string $status, $publishedAt): array
    {
        $status = strtolower(trim($status));

        if ($status === 'scheduled') {
            $status = 'published';
        }

        if (!in_array($status, ['draft','published','archived'], true)) {
            $status = 'draft';
        }

        $normalizedPublishedAt = null;
        if ($status === 'published' && filled($publishedAt)) {
            $normalizedPublishedAt = \Carbon\Carbon::parse($publishedAt);
        }

        if ($status !== 'published') {
            $normalizedPublishedAt = null;
        }

        return [$status, $normalizedPublishedAt];
    }

    private function computeReadingMinutes(?array $delta, ?string $html): int
    {
        $text = '';

        if (is_array($delta) && isset($delta['ops']) && is_array($delta['ops'])) {
            foreach ($delta['ops'] as $op) {
                $insert = $op['insert'] ?? null;
                if (is_string($insert)) {
                    $text .= ' ' . $insert;
                }
            }
        }

        if (trim($text) === '' && is_string($html) && $html !== '') {
            $text = strip_tags($html);
        }

        $text = trim(preg_replace('/\s+/u', ' ', (string)$text));
        if ($text === '') return 1;

        $words = preg_split('/\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY);
        $count = is_array($words) ? count($words) : 0;

        $wpm = 220;
        return max(1, (int)ceil($count / $wpm));
    }
}
