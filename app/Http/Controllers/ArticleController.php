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
        $sort    = (string)$request->get('sort', 'recent'); 
        $sort    = in_array($sort, ['recent','top','featured'], true) ? $sort : 'recent';

        $query = Article::query()
            ->select([
                'id','author_id','slug','hero_image',
                'title_id','title_en','title_ar',
                'excerpt_id','excerpt_en','excerpt_ar',
                'published_at','view_count','comment_count','reading_time',
            ])
            ->with(['author:id,name', 'categories:id,slug,name_id,name_en,name_ar', 'tags:id,slug,name'])
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

        $categoriesChip = Category::active()->ordered()
            ->limit(12)
            ->get(['id','slug','name_id','name_en','name_ar']);

        $tagsPopular = Tag::query()
            ->popular()
            ->withCount(['articles as published_articles_count' => fn($q) => $q->published()])
            ->having('published_articles_count', '>', 0)
            ->limit(20)
            ->get(['id','slug','name','use_count']);

        $top = Article::top()
            ->select(['id','slug','hero_image','title_id','title_en','title_ar','view_count','comment_count','published_at'])
            ->limit(5)->get();

        $featured = Article::featured()
            ->select(['id','slug','hero_image','title_id','title_en','title_ar','published_at'])
            ->limit(4)->get();

        return view('article.index', compact(
            'articles','categoriesChip','tagsPopular','top','featured',
            'q','catSlug','tagSlug','sort'
        ));
    }

    public function show(string $slug, Request $request)
    {
        $article = Article::query()
            ->select([
                'id','author_id','slug','hero_image',
                'title_id','title_en','title_ar',
                'excerpt_id','excerpt_en','excerpt_ar',
                'content_html_id','content_html_en','content_html_ar',
                'published_at','view_count','comment_count','reading_time',
            ])
            ->with(['author:id,name', 'categories:id,slug,name_id,name_en,name_ar', 'tags:id,slug,name'])
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        $key = 'viewed_article_'.$article->id;
        if (!$request->session()->has($key)) {
            $article->increment('view_count');
            $request->session()->put($key, true);
        }

        $replyTo = null;
        if ($rid = $request->integer('reply_to')) {
            $replyTo = $article->comments()
                ->approved()
                ->with('user:id,name,email')
                ->whereKey($rid)
                ->first();
        }

        // comments tree (approved)
        $allComments = $article->comments()
            ->approved()
            ->with('user:id,name,email')
            ->oldest()
            ->get();

        $related = Article::published()
            ->select(['id','slug','hero_image','title_id','title_en','title_ar','published_at','author_id'])
            ->with(['author:id,name', 'categories:id,slug,name_id,name_en,name_ar'])
            ->whereKeyNot($article->id)
            ->when($article->categories->isNotEmpty(), function($qq) use ($article){
                $catIds = $article->categories->pluck('id');
                $qq->whereHas('categories', fn($c) => $c->whereIn('categories.id', $catIds));
            })
            ->orderByDesc('published_at')
            ->limit(8)
            ->get();

        $top = Article::top()
            ->select(['id','slug','hero_image','title_id','title_en','title_ar','view_count','comment_count','published_at'])
            ->limit(5)->get();

        $featured = Article::featured()
            ->select(['id','slug','hero_image','title_id','title_en','title_ar','published_at'])
            ->limit(4)->get();

        $tagsPopular = Tag::query()
            ->popular()
            ->withCount(['articles as published_articles_count' => fn($q) => $q->published()])
            ->having('published_articles_count', '>', 0)
            ->limit(20)
            ->get(['id','slug','name','use_count']);

        $comments = $article->comments()
            ->approved()
            ->with('user:id,name,email')
            ->oldest()
            ->get();

        $groupedComments = $comments->groupBy('parent_id');
        $topLevelComments = $groupedComments[null] ?? collect();

        return view('article.show', compact(
            'article','related','top','featured','tagsPopular',
            'replyTo','groupedComments','topLevelComments', 'comments', 
        ));
    }
}
