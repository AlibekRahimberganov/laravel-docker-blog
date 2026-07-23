<x-layout>
    <div class="tm-hero d-flex justify-content-center align-items-center" data-parallax="scroll" data-image-src="{{ asset('img/hero.jpg') }}">
        <!-- optional search form could go here -->
    </div>

    <div class="container-fluid tm-container-content tm-mt-60">
        @if ($recommendedPosts->isNotEmpty())
            <div class="row mb-4">
                <h2 class="tm-text-primary">
                    Recommended
                </h2>
            </div>

            <div class="row tm-mb-50 tm-gallery" style="margin-left: -8px; margin-right: -8px;">
                @foreach ($recommendedPosts->take(4) as $post)
                    @include('partials.post-card', ['post' => $post])
                @endforeach
            </div>

            @if ($recommendedPosts->count() > 4)
                <div id="recommended-more" class="row tm-mb-50 tm-gallery" style="margin-left: -8px; margin-right: -8px; display: none;">
                    @foreach ($recommendedPosts->skip(4) as $post)
                        @include('partials.post-card', ['post' => $post])
                    @endforeach
                </div>
            @endif

            @if ($recommendedPosts->count() > 4)
                <div class="row tm-mb-90">
                    <div class="col-12 d-flex justify-content-center">
                        <button type="button" id="recommended-toggle" class="btn">Show more</button>
                    </div>
                </div>
            @endif
        @endif

        <div class="row mb-4">
            <h2 class="tm-text-primary">
                Latest Posts
            </h2>
        </div>

        <div class="row tm-mb-90 tm-gallery" style="margin-left: -8px; margin-right: -8px;">
            @foreach ($posts as $post)
                @include('partials.post-card', ['post' => $post])
            @endforeach
        </div>

        <div class="row tm-mb-90">
            <div class="col-12 d-flex justify-content-center">
                {{ $posts->links() }}
            </div>
        </div>
    </div>
</x-layout>