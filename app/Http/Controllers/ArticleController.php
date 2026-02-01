<?php

namespace App\Http\Controllers;

use App\Models\{Article, Category, Tag};
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $q       = trim((string)$request->get('q', ''));
        $catSlug = $request->get('category');
        $tagSlug = $request->get('tag');
        $sort    = (string)$request->get('sort', 'recent'); // recent|top|featured

        $query = Article::with(['author','categories','tags'])
            ->when($q !== '', fn($qq) => $qq->searchTitle($q))
            ->when($catSlug, fn($qq) => $qq->whereHas('categories', fn($c) => $c->where('slug', $catSlug)))
            ->when($tagSlug, fn($qq) => $qq->whereHas('tags', fn($t) => $t->where('slug', $tagSlug)))
            ->published();

        if ($sort === 'top') {
            $query->orderByDesc('view_count');
        } elseif ($sort === 'featured') {
            $query->where('is_featured', true)->orderByDesc('published_at');
        } else {
            $query->orderByDesc('published_at');
        }

        $articles = $query->paginate(12)->withQueryString();
        $categories = Category::active()->ordered()->get();
        $tags = Tag::popular()->limit(20)->get();

        return view('article', compact('articles','categories','tags','q','catSlug','tagSlug','sort'));
    }

    public function show(string $slug, Request $request)
    {
        $article = Article::with(['author','categories','tags'])
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        // view counter tetap (session-based)
        $key = 'viewed_article_'.$article->id;
        if (!$request->session()->has($key)) {
            $article->increment('view_count');
            $request->session()->put($key, true);
        }

        // reply_to tetap (biar komentar UI kamu ga rusak)
        $replyTo = null;
        if ($rid = $request->integer('reply_to')) {
            $replyTo = $article->comments()
                ->approved()
                ->with('user:id,name,email')
                ->whereKey($rid)
                ->first();
        }

        $related = Article::published()
            ->whereKeyNot($article->id)
            ->when($article->categories->isNotEmpty(), function($qq) use ($article){
                $catIds = $article->categories->pluck('id');
                $qq->whereHas('categories', fn($c) => $c->whereIn('categories.id', $catIds));
            })
            ->orderByDesc('published_at')
            ->limit(8)
            ->get();

        $top      = Article::top()->limit(5)->get();
        $featured = Article::featured()->limit(4)->get();

        // view lama kamu pakai view('article') untuk list+detail,
        // jadi tetap sama biar kamu nggak nangis di blade.
        return view('article', compact('article','related','top','featured','replyTo'));
    }
}
