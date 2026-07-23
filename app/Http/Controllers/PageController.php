<?php

namespace App\Http\Controllers;

use App\Models\Posts;
use App\Models\PostView;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

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
        $users = User::where('id', '!=', Auth::id())->orderBy('login')->get();

        return view('create', compact('categories', 'users'));
    }

    public function show_specific_post(Posts $post)
    {
        /* Showing specific post and tracking views */
        $viewedKey = 'viewed_post_'.$post->id;
        if (! Session::has($viewedKey)) {
            PostView::create([
                'post_id' => $post->id,
                'user_id' => Auth::id(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
            Session::put($viewedKey, now()->toDateTimeString());
        }

        $post->load(['coAuthors.user', 'tags']);

        return view('post', ['post' => $post]);
    }

    public function show()
    {
        /* Showing all posts on main page */
        $posts = Posts::with(['tags'])->orderBy('created_at', 'desc')->paginate(50);

        // Placeholder recommendation logic: most-recommended posts first, falling back
        // to newest — the actual recommendation algorithm is still to be designed.
        $recommendedPosts = Posts::with(['tags'])
            ->withCount(['reactions as recommends_count' => fn ($query) => $query->where('type', 'recommend')])
            ->orderByDesc('recommends_count')
            ->orderByDesc('created_at')
            ->take(12)
            ->get();

        return view('welcome', ['posts' => $posts, 'recommendedPosts' => $recommendedPosts]);
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
        /* Showing user profile, including posts they co-author, friends, and pending requests */
        $user = Auth::user();
        $posts = $user->visiblePosts()->orderBy('created_at', 'desc')->paginate(50);
        $friends = $user->friends()->get();
        $incomingRequests = $user->receivedFriendRequests()->where('status', 'pending')->with('sender')->get();
        $outgoingRequests = $user->sentFriendRequests()->where('status', 'pending')->with('receiver')->get();
        $following = $user->following()->get();
        $followers = $user->followers()->get();

        return view('profile', [
            'user' => $user,
            'posts' => $posts,
            'friends' => $friends,
            'incomingRequests' => $incomingRequests,
            'outgoingRequests' => $outgoingRequests,
            'following' => $following,
            'followers' => $followers,
        ]);
    }

    public function authorProfile(User $user)
    {
        /* Public profile page for viewing another user's (and their co-authored) posts */
        $posts = $user->visiblePosts()->orderBy('created_at', 'desc')->paginate(50);
        $friendship = Auth::check() ? Auth::user()->friendshipWith($user) : null;
        $isFollowing = Auth::check() ? Auth::user()->isFollowing($user) : false;

        return view('author-profile', [
            'user' => $user,
            'posts' => $posts,
            'friendship' => $friendship,
            'isFollowing' => $isFollowing,
            'followersCount' => $user->followers()->count(),
            'followingCount' => $user->following()->count(),
        ]);
    }

    public function showTag(Tag $tag)
    {
        /* Showing all posts under a given tag */
        $posts = $tag->posts()->orderBy('created_at', 'desc')->paginate(50);

        return view('tag', ['tag' => $tag, 'posts' => $posts]);
    }

    public function favourites()
    {
        /* Showing posts the current user has recommended */
        $posts = Posts::whereHas('reactions', function ($query) {
            $query->where('user_id', Auth::id())->where('type', 'recommend');
        })->with(['category', 'tags'])->orderBy('created_at', 'desc')->paginate(50);

        return view('favourites', ['posts' => $posts]);
    }
}
