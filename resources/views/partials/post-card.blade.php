<div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 col-12 mb-5" style="padding-left: 8px; padding-right: 8px;">
    <figure class="effect-ming tm-video-item">
        @php $path = $post->content_media ?? null; @endphp
        @if ($path)
            @switch(strtolower(pathinfo($path, PATHINFO_EXTENSION)))
                @case('jpg')
                @case('jpeg')
                @case('png')
                @case('gif')
                    <img src="{{ asset('storage/' . $path) }}" alt="{{ $post->title }}" class="img-fluid">
                    @break
                @case('mp4')
                    <video controls class="img-fluid" style="width: 100%; height: auto;">
                        <source src="{{ asset('storage/' . $path) }}" type="video/mp4">
                    </video>
                    @break
            @endswitch
        @else
            <!-- Text preview when no media -->
            <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 280px; display: flex; flex-direction: column; justify-content: center; align-items: center; padding: 30px; position: relative; color: white;">
                <div style="flex: 1; display: flex; align-items: center; justify-content: center; text-align: center; width: 100%;">
                    <p style="font-size: 0.95rem; line-height: 1.6; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 5; -webkit-box-orient: vertical; margin: 0;">
                        {{ Illuminate\Support\Str::limit(strip_tags($post->content), 200, '...') }}
                    </p>
                </div>
            </div>
        @endif
        <figcaption class="d-flex align-items-center justify-content-center">
            <h2>{{ $post->title }}</h2>
            <a href="{{ route('blog.post', $post) }}">View more</a>
        </figcaption>
    </figure>
    <div class="d-flex justify-content-between tm-text-gray" style="padding: 0 8px;">
        <span class="tm-text-gray-light">{{ $post->created_at->format('d M Y') }}</span>
        <div class="d-flex gap-3 text-xs">
            <span title="Views"><i class="fas fa-eye"></i> {{ $post->views_count }}</span>
            <span title="Likes"><i class="fas fa-thumbs-up"></i> {{ $post->likes_count }}</span>
            <span title="Dislikes"><i class="fas fa-thumbs-down"></i> {{ $post->dislikes_count }}</span>
            <span title="Recommendations"><i class="fas fa-star text-yellow-500"></i> {{ $post->recommends_count }}</span>
        </div>
    </div>
</div>
