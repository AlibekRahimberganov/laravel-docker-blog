<x-layout>
    <h2 class="text-center">{{ $post->title }}</h2>
    @php
        $path = $post->content_media ?? null;
    @endphp
    @if ($path)
        <div class="flex justify-center my-4">
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
                <div class="w-full max-w-md">
                    @if ($post->content_media_cover)
                        <img src="{{ asset('storage/' . $post->content_media_cover) }}" alt="Cover art" class="rounded-lg mb-2 mx-auto max-h-64">
                    @endif
                    @if ($post->content_media_artist)
                        <p class="text-center text-gray-500 mb-2">{{ $post->content_media_artist }}</p>
                    @endif
                    <audio controls class="w-full">
                        <source src="{{ asset('storage/' . $path) }}" alt="audio_file" type="audio/mpeg">
                    </audio>
                </div>
                @break
            @endswitch
        </div>
    @endif
    <div style="white-space: pre-wrap;">{{ $post->content }}</div>

    <div class="flex items-center gap-8 my-6" data-post-id="{{ $post->id }}">
        <span class="flex items-center gap-1 text-gray-500" title="Views">
            <i class="fas fa-eye"></i>
            <span>{{ $post->views_count }}</span>
        </span>

        @auth
            <button type="button" class="reaction-btn flex items-center gap-1 {{ $post->hasReaction('like') ? 'text-blue-600' : 'text-gray-500' }} hover:text-blue-700 transition"
                data-type="like" data-url="{{ route('post.react', ['post' => $post->id, 'type' => 'like']) }}">
                <i class="fas fa-thumbs-up"></i>
                <span class="reaction-count">{{ $post->likes_count }}</span>
            </button>

            <button type="button" class="reaction-btn flex items-center gap-1 {{ $post->hasReaction('dislike') ? 'text-red-600' : 'text-gray-500' }} hover:text-red-700 transition"
                data-type="dislike" data-url="{{ route('post.react', ['post' => $post->id, 'type' => 'dislike']) }}">
                <i class="fas fa-thumbs-down"></i>
                <span class="reaction-count">{{ $post->dislikes_count }}</span>
            </button>

            <button type="button" class="reaction-btn flex items-center gap-1 {{ $post->hasReaction('recommend') ? 'text-yellow-500' : 'text-gray-500' }} hover:text-yellow-600 transition"
                data-type="recommend" data-url="{{ route('post.react', ['post' => $post->id, 'type' => 'recommend']) }}">
                <i class="fas fa-star"></i>
                <span class="reaction-count">{{ $post->recommends_count }}</span>
            </button>
        @else
            <a href="{{ route('show.login') }}" class="flex items-center gap-1 text-gray-500" title="Log in to react">
                <i class="fas fa-thumbs-up"></i>
                <span>{{ $post->likes_count }}</span>
            </a>
            <a href="{{ route('show.login') }}" class="flex items-center gap-1 text-gray-500" title="Log in to react">
                <i class="fas fa-thumbs-down"></i>
                <span>{{ $post->dislikes_count }}</span>
            </a>
            <a href="{{ route('show.login') }}" class="flex items-center gap-1 text-gray-500" title="Log in to react">
                <i class="fas fa-star"></i>
                <span>{{ $post->recommends_count }}</span>
            </a>
        @endauth
    </div>

    <h4>Author: <b><a href="{{ route('blog.author', $post->user) }}">{{ $post->user->login }}</a></b></h4>
    @if ($post->coAuthors->isNotEmpty())
        <h5>Co-authors:
            @foreach ($post->coAuthors as $coAuthor)
                @if ($coAuthor->user)
                    <a href="{{ route('blog.author', $coAuthor->user) }}">{{ $coAuthor->user->login }}</a>@if (! $loop->last), @endif
                @else
                    {{ $coAuthor->name }}@if (! $loop->last), @endif
                @endif
            @endforeach
        </h5>
    @endif
    <h5>Category: <b>{{ $post->category->name }}</b></h5>
    @if ($post->tags->isNotEmpty())
        <div class="my-2">
            @foreach ($post->tags as $tag)
                <a href="{{ route('blog.tag', $tag) }}" class="btn text-xs mr-2">#{{ $tag->name }}</a>
            @endforeach
        </div>
    @endif

    @auth
        @if ($post->isEditableBy(Auth::user()))
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