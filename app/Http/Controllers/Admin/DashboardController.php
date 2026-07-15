<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Posts;
use App\Models\PostView;
use App\Models\Reaction;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'users_count' => User::count(),
            'posts_count' => Posts::count(),
            'categories_count' => Category::count(),
            'views_count' => PostView::count(),
            'likes_count' => Reaction::where('type', 'like')->count(),
            'dislikes_count' => Reaction::where('type', 'dislike')->count(),
            'recommends_count' => Reaction::where('type', 'recommend')->count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
