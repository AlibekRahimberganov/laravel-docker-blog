<x-layout>
    <div class="flex justify-between items-center my-8">
        <div class="flex items-center gap-4">
            <img src="{{ $user->avatar_url }}" alt="{{ $user->login }}" class="w-28 h-28 rounded-full object-cover">
            <div>
                <h1 class="text-3xl font-bold">{{ $user->login }}</h1>
                <p class="text-gray-600">Joined: {{ $user->created_at->format('d M Y') }}</p>
                <p class="text-gray-600 text-sm mt-1">{{ $followersCount }} followers · {{ $followingCount }} following</p>
                @if ($user->bio)
                    <p class="text-gray-700 mt-2 max-w-xl">{{ $user->bio }}</p>
                @endif
            </div>
        </div>

        @auth
            @if (Auth::id() !== $user->id)
                <div class="flex flex-col items-end gap-2">
                    @if ($isFollowing)
                        <form action="{{ route('follow.destroy', $user) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn">Following</button>
                        </form>
                    @else
                        <form action="{{ route('follow.store', $user) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn bg-blue-100 hover:bg-blue-500">Follow</button>
                        </form>
                    @endif

                    @if (! $friendship)
                        <form action="{{ route('friends.request', $user) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn">Add Friend</button>
                        </form>
                    @elseif ($friendship->status === 'accepted')
                        <div class="flex items-center gap-2">
                            <span class="text-green-600"><i class="fas fa-user-check"></i> Friends</span>
                            <a href="{{ route('messages.show', $user) }}" class="btn">Message</a>
                            <form action="{{ route('friends.destroy', $friendship) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn bg-red-100 hover:bg-red-500">Unfriend</button>
                            </form>
                        </div>
                    @elseif ($friendship->sender_id === Auth::id())
                        <div class="flex items-center gap-2">
                            <span class="text-gray-500">Request Pending</span>
                            <form action="{{ route('friends.destroy', $friendship) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn">Cancel</button>
                            </form>
                        </div>
                    @else
                        <div class="flex items-center gap-2">
                            <form action="{{ route('friends.accept', $friendship) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn bg-green-100 hover:bg-green-500">Accept</button>
                            </form>
                            <form action="{{ route('friends.destroy', $friendship) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn bg-red-100 hover:bg-red-500">Reject</button>
                            </form>
                        </div>
                    @endif
                </div>
            @endif
        @endauth
    </div>

    <h2 class="text-2xl font-bold mb-4">Posts</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @forelse ($posts as $post)
            <div class="card p-4">
                <div class="flex-1">
                    <h3 class="text-xl font-semibold mb-2">{{ $post->title }}</h3>
                    <p class="text-gray-500 text-sm mb-4">{{ $post->created_at->format('d M Y') }}</p>
                    <div class="flex gap-4 text-sm text-gray-600">
                        <span><i class="fas fa-eye"></i> {{ $post->views_count }}</span>
                        <span><i class="fas fa-thumbs-up"></i> {{ $post->likes_count }}</span>
                    </div>
                    <a href="{{ route('blog.post', $post) }}" class="reg_btn mt-4 block">View Post</a>
                </div>
            </div>
        @empty
            <p class="text-gray-500">No posts yet.</p>
        @endforelse
    </div>

    <div class="my-8">
        {{ $posts->links() }}
    </div>
</x-layout>
