<?php

namespace App\Http\Controllers;

use App\Models\Posts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class StatsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $posts = $user->posts()
            ->withCount(['views', 'reactions as likes_count' => function($query) {
                $query->where('type', 'like');
            }, 'reactions as dislikes_count' => function($query) {
                $query->where('type', 'dislike');
            }, 'reactions as recommends_count' => function($query) {
                $query->where('type', 'recommend');
            }])
            ->orderBy('views_count', 'desc')
            ->get();

        $totalViews = $posts->sum('views_count');
        $topPost = $posts->first();

        return view('stats', compact('posts', 'totalViews', 'topPost'));
    }

    public function exportCsv()
    {
        $user = Auth::user();
        $posts = $user->posts()
            ->withCount(['views', 'reactions as likes_count' => function($query) {
                $query->where('type', 'like');
            }, 'reactions as dislikes_count' => function($query) {
                $query->where('type', 'dislike');
            }, 'reactions as recommends_count' => function($query) {
                $query->where('type', 'recommend');
            }])
            ->get();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=post_statistics.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Title', 'Category', 'Views', 'Likes', 'Dislikes', 'Recommendations', 'Created At'];

        $callback = function() use($posts, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($posts as $post) {
                fputcsv($file, [
                    $post->title,
                    $post->category->name,
                    $post->views_count,
                    $post->likes_count,
                    $post->dislikes_count,
                    $post->recommends_count,
                    $post->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}
