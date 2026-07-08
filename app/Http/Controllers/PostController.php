<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    // Public Views
    public function publicIndex()
    {
        $posts = Post::where('is_published', true)->latest()->paginate(9);
        return view('public.blog.index', compact('posts'));
    }

    public function publicShow($slug)
    {
        $post = Post::where('slug', $slug)->where('is_published', true)->firstOrFail();
        $post->increment('views_count');
        $relatedPosts = Post::where('id', '!=', $post->id)
            ->where('is_published', true)
            ->latest()
            ->take(3)
            ->get();
        return view('public.blog.show', compact('post', 'relatedPosts'));
    }

    // Admin Views
    public function index()
    {
        $posts = Post::latest()->paginate(10);
        return view('post.index', compact('posts'));
    }

    public function create()
    {
        return view('post.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $post = new Post();
        $post->title = $request->title;
        $post->slug = Str::slug($request->title);
        $post->content = $request->content;
        $post->is_published = $request->has('is_published');

        // SEO & Metadata
        $post->meta_title = $request->meta_title ?: $request->title;
        $post->meta_description = $request->meta_description ?: Str::limit(strip_tags($request->content), 150);
        $post->meta_keywords = $request->meta_keywords ?: 'blog, lodge, travel, luxury, ' . strtolower($request->title);
        $post->excerpt = $request->excerpt ?: Str::limit(strip_tags($request->content), 160);
        
        $wordCount = str_word_count(strip_tags($request->content));
        $post->read_time = $request->read_time ?: max(1, ceil($wordCount / 200));

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('img/posts'), $filename);
            $post->image = 'img/posts/' . $filename;
        }

        $post->save();

        return redirect()->route('post.index')->with('success', 'Post created successfully.');
    }

    public function edit(Post $post)
    {
        return view('post.edit', compact('post'));
    }

    public function update(Request $request, Post $post)
    {
        $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $post->title = $request->title;
        $post->slug = Str::slug($request->title);
        $post->content = $request->content;
        $post->is_published = $request->has('is_published');

        // SEO & Metadata
        $post->meta_title = $request->meta_title ?: $request->title;
        $post->meta_description = $request->meta_description ?: Str::limit(strip_tags($request->content), 150);
        $post->meta_keywords = $request->meta_keywords ?: 'blog, lodge, travel, luxury, ' . strtolower($request->title);
        $post->excerpt = $request->excerpt ?: Str::limit(strip_tags($request->content), 160);
        
        $wordCount = str_word_count(strip_tags($request->content));
        $post->read_time = $request->read_time ?: max(1, ceil($wordCount / 200));

        if ($request->hasFile('image')) {
            // Delete old image if it exists
            if ($post->image && file_exists(public_path($post->image))) {
                unlink(public_path($post->image));
            }
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('img/posts'), $filename);
            $post->image = 'img/posts/' . $filename;
        }

        $post->save();

        return redirect()->route('post.index')->with('success', 'Post updated successfully.');
    }

    public function destroy(Post $post)
    {
        if ($post->image && file_exists(public_path($post->image))) {
            unlink(public_path($post->image));
        }
        $post->delete();
        return redirect()->route('post.index')->with('success', 'Post deleted successfully.');
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif|max:5000',
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('img/posts/content'), $filename);

            return response()->json([
                'location' => asset('img/posts/content/' . $filename)
            ]);
        }

        return response()->json(['error' => 'No file uploaded'], 400);
    }
}
