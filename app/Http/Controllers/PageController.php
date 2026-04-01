<?php

namespace App\Http\Controllers;

use App\Models\Posts;
use Illuminate\Support\Facades\Auth;

class PageController extends Controller
{
    public function showlogin()
    {
        return view('login');
    }
    public function create()
    {
        /* Routing to create post page */
        $categories = \App\Models\Category::orderBy('name')->get();
        return view('create', compact('categories'));
    }
    public function show_specific_post(Posts $post)
    {
        /* Showing specific post */
        return view('post', ['post' => $post]);
    }
    public function show()
    {
        /* Showing all posts on main page */
        $posts = Posts::orderBy('created_at', 'desc')->paginate(10);
        return view('welcome', ['posts' => $posts]);
    }
    public function about()
    {
        /* Showing about page */
        return view('about');
    }

    public function contact()
    {
        /* render contact page; form processing could be added later */
        return view('contact');
    }
    public function profile()
    {
        /* Showing user profile */
        $user = Auth::user();
        $posts = $user->posts()->orderBy('created_at', 'desc')->paginate(10);
        return view('profile', ['user' => $user, 'posts' => $posts]);
    }
}
