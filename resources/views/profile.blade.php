<x-layout>
    <div class="flex justify-between items-center my-8">
        <div class="flex items-center gap-4">
            <img src="{{ $user->avatar_url }}" alt="{{ $user->login }}" class="w-28 h-28 rounded-full object-cover">
            <div>
                <h1 class="text-3xl font-bold">{{ $user->login }}</h1>
                <p class="text-gray-600">Email: {{ $user->email }}</p>
                <p class="text-gray-600">Joined: {{ $user->created_at->format('d M Y') }}</p>
                <p class="text-gray-600 text-sm mt-1">{{ $followers->count() }} followers · {{ $following->count() }} following</p>
                @if ($user->bio)
                    <p class="text-gray-700 mt-2 max-w-xl">{{ $user->bio }}</p>
                @endif
            </div>
        </div>
        <div class="flex gap-4">
            <a href="{{ route('blog.profile.edit') }}" class="btn">Edit Profile</a>
            <a href="{{ route('blog.stats') }}" class="btn">View Statistics</a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn bg-red-100 hover:bg-red-500">Logout</button>
            </form>
        </div>
    </div>

    @if ($incomingRequests->isNotEmpty())
        <h2 class="text-2xl font-bold mb-4">Friend Requests</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
            @foreach ($incomingRequests as $request)
                <div class="card p-4 flex items-center justify-between">
                    <a href="{{ route('blog.author', $request->sender) }}" class="flex items-center gap-3">
                        <img src="{{ $request->sender->avatar_url }}" alt="{{ $request->sender->login }}" class="w-10 h-10 rounded-full object-cover">
                        {{ $request->sender->login }}
                    </a>
                    <div class="flex gap-2">
                        <form action="{{ route('friends.accept', $request) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn bg-green-100 hover:bg-green-500">Accept</button>
                        </form>
                        <form action="{{ route('friends.destroy', $request) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn bg-red-100 hover:bg-red-500">Reject</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    @if ($outgoingRequests->isNotEmpty())
        <h2 class="text-2xl font-bold mb-4">Sent Requests</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
            @foreach ($outgoingRequests as $request)
                <div class="card p-4 flex items-center justify-between">
                    <a href="{{ route('blog.author', $request->receiver) }}" class="flex items-center gap-3">
                        <img src="{{ $request->receiver->avatar_url }}" alt="{{ $request->receiver->login }}" class="w-10 h-10 rounded-full object-cover">
                        {{ $request->receiver->login }}
                    </a>
                    <form action="{{ route('friends.destroy', $request) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn">Cancel</button>
                    </form>
                </div>
            @endforeach
        </div>
    @endif

    <h2 class="text-2xl font-bold mb-4">Friends ({{ $friends->count() }})</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
        @forelse ($friends as $friend)
            <a href="{{ route('blog.author', $friend) }}" class="card p-4 flex items-center gap-3">
                <img src="{{ $friend->avatar_url }}" alt="{{ $friend->login }}" class="w-10 h-10 rounded-full object-cover">
                {{ $friend->login }}
            </a>
        @empty
            <p class="text-gray-500">No friends yet.</p>
        @endforelse
    </div>

    <h2 class="text-2xl font-bold mb-4">Following ({{ $following->count() }})</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
        @forelse ($following as $followedUser)
            <a href="{{ route('blog.author', $followedUser) }}" class="card p-4 flex items-center gap-3">
                <img src="{{ $followedUser->avatar_url }}" alt="{{ $followedUser->login }}" class="w-10 h-10 rounded-full object-cover">
                {{ $followedUser->login }}
            </a>
        @empty
            <p class="text-gray-500">You're not following anyone yet.</p>
        @endforelse
    </div>

    <h2 class="text-2xl font-bold mb-4">My Posts</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach ($posts as $post)
            <div class="card p-4">
                <div class="flex-1">
                    <h3 class="text-xl font-semibold mb-2">
                        {{ $post->title }}
                        @if ($post->user_id !== $user->id)
                            <span class="text-xs text-gray-400 font-normal">(co-authored)</span>
                        @endif
                    </h3>
                    <p class="text-gray-500 text-sm mb-4">{{ $post->created_at->format('d M Y') }}</p>
                    <div class="flex gap-4 text-sm text-gray-600">
                        <span><i class="fas fa-eye"></i> {{ $post->views_count }}</span>
                        <span><i class="fas fa-thumbs-up"></i> {{ $post->likes_count }}</span>
                    </div>
                    <a href="{{ route('blog.post', $post) }}" class="reg_btn mt-4 block">View Post</a>
                </div>
            </div>
        @endforeach
    </div>

    <div class="my-8">
        {{ $posts->links() }}
    </div>
</x-layout>
