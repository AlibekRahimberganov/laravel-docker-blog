<x-layout>
    <h2>{{ $post->title }}</h2>
    @php
        $path = $post->content_media ?? null;
    @endphp
    @if ($path)
        @switch(strtolower(pathinfo($path, PATHINFO_EXTENSION)))
            
            @case('jpg')
            @case('jpeg')
            @case('png')
            @case('gif')
            <img src="{{ asset('storage/' . $path) }}" alt="Picture" class="max-w-full rounded-lg">
            @break

            @case('mp4')
            <video controls class="max-w-full rounded-lg">
                <source src="{{ asset('storage/' . $path) }}" alt="video_file" type="video/mp4">
            </video>
            @break

            @case('mp3')
            <audio controls class="w-full">
                <source src="{{ asset('storage/' . $path) }}" alt="audio_file" type="audio/mpeg">
            </audio>
            @break
        @endswitch
    @endif
    <div style="white-space: pre-wrap;">{{ $post->content }}</div>

    <div class="flex items-center gap-4 my-6">
        <form action="{{ route('post.react', ['post' => $post->id, 'type' => 'like']) }}" method="POST">
            @csrf
            <button type="submit" class="flex items-center gap-1 {{ $post->hasReaction('like') ? 'text-blue-600' : 'text-gray-500' }} hover:text-blue-700 transition">
                <i class="fas fa-thumbs-up"></i>
                <span>{{ $post->likes_count }}</span>
            </button>
        </form>

        <form action="{{ route('post.react', ['post' => $post->id, 'type' => 'dislike']) }}" method="POST">
            @csrf
            <button type="submit" class="flex items-center gap-1 {{ $post->hasReaction('dislike') ? 'text-red-600' : 'text-gray-500' }} hover:text-red-700 transition">
                <i class="fas fa-thumbs-down"></i>
                <span>{{ $post->dislikes_count }}</span>
            </button>
        </form>

        <form action="{{ route('post.react', ['post' => $post->id, 'type' => 'recommend']) }}" method="POST">
            @csrf
            <button type="submit" class="flex items-center gap-1 {{ $post->hasReaction('recommend') ? 'text-yellow-500' : 'text-gray-500' }} hover:text-yellow-600 transition">
                <i class="fas fa-star"></i>
                <span>{{ $post->recommends_count }}</span>
            </button>
        </form>
    </div>

    <h4>Author: <b>{{ $post->user->login }}</b></h4>
    <h5>Category: <b>{{ $post->category->name }}</b></h5>

    @auth
        @if (Auth::user()->id == $post->user_id)
            <form action="{{ route('blog.delete', ['post' => $post->id]) }}" method="POST">
                @csrf
                @method('DELETE')
                <button class="btn my-4">Delete this post</button>
            </form>
            <form action="{{ route('blog.edit', ['post' => $post->id]) }}" method="GET">
                @csrf
                <button class="btn my-4">Edit this post</button>
            </form>
        @endif
    @endauth
</x-layout>