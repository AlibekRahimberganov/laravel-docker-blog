<x-layout>
    <div class="flex justify-between items-center my-8">
        <div class="flex items-center gap-4">
            <img src="{{ $user->avatar_url }}" alt="{{ $user->login }}" class="w-28 h-28 rounded-full object-cover">
            <div>
                <h1 class="text-3xl font-bold">{{ $user->login }}</h1>
                <p class="text-gray-600">Email: {{ $user->email }}</p>
                <p class="text-gray-600">Joined: {{ $user->created_at->format('d M Y') }}</p>
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
