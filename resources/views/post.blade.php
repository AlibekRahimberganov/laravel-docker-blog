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