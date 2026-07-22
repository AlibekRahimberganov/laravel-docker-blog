<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Posts;

class PostController extends Controller
{
    public function index()
    {
        $posts = Posts::with(['user', 'category'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.posts.index', compact('posts'));
    }

    public function destroy(Posts $post)
    {
        $post->delete();

        return back()->with('success', 'Post deleted.');
    }
}
