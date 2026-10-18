<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use App\Models\Service;
use App\Models\Tag;
use App\Models\User;
use App\Services\SeoInternalLinker;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogController extends Controller
{
    /**
     * Display a listing of blog posts.
     */
    public function index(Request $request): View
    {
        $search = $request->query('q');

        $query = Post::published()->with(['user', 'categories', 'tags'])->latest('published_at');

        if ($search) {
            $query->search($search);
        }

        $posts = $query->paginate(9)->withQueryString();
        $categories = Category::withCount(['posts' => fn ($q) => $q->where('status', 'published')])->get();
        $tags = Tag::withCount(['posts' => fn ($q) => $q->where('status', 'published')])->get();

        return view('frontend.blog.index', compact('posts', 'search', 'categories', 'tags'));
    }

    /**
     * Display the specified blog post with SEO and related content.
     */
    public function show(string $slug, ?SeoInternalLinker $linker = null): View|RedirectResponse
    {
        $linker = $linker ?? app(SeoInternalLinker::class);

        $postQuery = Post::with(['user', 'categories', 'tags'])
            ->where('slug', $slug);

        // If not logged in as content manager, only show published posts
        if (! auth()->check() || ! auth()->user()->canManageContent()) {
            $postQuery->where('status', 'published')
                ->where('published_at', '<=', now());
        }

        $post = $postQuery->first();

        if (! $post) {
            // Gracefully 301 redirect legacy/retired portacabin and site compound articles to the official Portacabin service page
            if (str_contains($slug, 'portacabin') || str_contains($slug, 'cabin') || str_contains($slug, 'compound') || str_contains($slug, 'ablution')) {
                return redirect('/portacabin', 301);
            }

            abort(404);
        }

        // Match targeted commercial recovery service for contextual internal linking
        $targetedService = $linker->matchServiceForPost($post);

        // Related posts in same categories
        $categoryIds = $post->categories->pluck('id')->toArray();
        $relatedPosts = Post::published()
            ->where('id', '!=', $post->id)
            ->whereHas('categories', fn ($q) => $q->whereIn('categories.id', $categoryIds))
            ->with(['user', 'categories'])
            ->latest('published_at')
            ->take(3)
            ->get();

        // Prev and Next posts
        $previousPost = Post::published()
            ->where('published_at', '<', $post->published_at ?? now())
            ->latest('published_at')
            ->first();

        $nextPost = Post::published()
            ->where('published_at', '>', $post->published_at ?? now())
            ->oldest('published_at')
            ->first();

        $services = Service::published()->ordered()->get();

        return view('frontend.blog.show', compact('post', 'relatedPosts', 'previousPost', 'nextPost', 'targetedService', 'services'));
    }

    /**
     * Display posts for a specific category.
     */
    public function category(string $slug): View
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        $posts = $category->publishedPosts()
            ->with(['user', 'categories', 'tags'])
            ->latest('published_at')
            ->paginate(9);

        $categories = Category::withCount(['posts' => fn ($q) => $q->where('status', 'published')])->get();

        return view('frontend.blog.category', compact('category', 'posts', 'categories'));
    }

    /**
     * Display posts for a specific tag.
     */
    public function tag(string $slug): View
    {
        $tag = Tag::where('slug', $slug)->firstOrFail();

        $posts = $tag->publishedPosts()
            ->with(['user', 'categories', 'tags'])
            ->latest('published_at')
            ->paginate(9);

        $tags = Tag::withCount(['posts' => fn ($q) => $q->where('status', 'published')])->get();

        return view('frontend.blog.tag', compact('tag', 'posts', 'tags'));
    }

    /**
     * Display posts for a specific author or redirect legacy numeric ID.
     */
    public function author(string $slug): View|RedirectResponse
    {
        if (is_numeric($slug)) {
            $user = User::find((int) $slug);
            if ($user && ! empty($user->slug)) {
                return redirect()->route('author.show', $user->slug, 301);
            }

            return redirect()->route('blog.index', [], 301);
        }

        $author = User::where('slug', $slug)->firstOrFail();

        $posts = $author->posts()
            ->published()
            ->with(['user', 'categories', 'tags'])
            ->latest('published_at')
            ->paginate(9);

        return view('frontend.blog.author', compact('author', 'posts'));
    }
}
