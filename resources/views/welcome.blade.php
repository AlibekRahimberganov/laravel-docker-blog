<x-layout>
    <div class="tm-hero d-flex justify-content-center align-items-center" data-parallax="scroll" data-image-src="{{ asset('img/hero.jpg') }}">
        <!-- optional search form could go here -->
    </div>

    <div class="container-fluid tm-container-content tm-mt-60">
        <div class="row mb-4">
            <h2 class="tm-text-primary">
                Latest Posts
            </h2>
        </div>

        <div class="row tm-mb-90 tm-gallery" style="margin-left: -8px; margin-right: -8px;">
            @foreach ($posts as $post)
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
                    </div>
                </div>
            @endforeach
        </div>

        <div class="row tm-mb-90">
            <div class="col-12 d-flex justify-content-center">
                {{ $posts->links() }}
            </div>
        </div>
    </div>
</x-layout>