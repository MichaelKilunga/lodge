<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Room;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $rooms = Room::all();
        $posts = Post::where('is_published', true)->latest()->get();

        return response()->view('template.sitemap', compact('rooms', 'posts'), 200, [
            'Content-Type' => 'application/xml'
        ]);
    }
}
