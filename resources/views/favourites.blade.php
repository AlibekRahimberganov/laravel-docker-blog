<x-layout>
    <div class="container-fluid tm-container-content tm-mt-60">
        <div class="row mb-4">
            <h2 class="tm-text-primary">
                My Favourites
            </h2>
        </div>

        <div class="row tm-mb-90 tm-gallery" style="margin-left: -8px; margin-right: -8px;">
            @forelse ($posts as $post)
                @include('partials.post-card', ['post' => $post])
            @empty
                <div class="col-12 text-center py-5">
                    <p class="tm-text-gray">You haven't recommended any posts yet.</p>
                </div>
            @endforelse
        </div>

        <div class="row tm-mb-90">
            <div class="col-12 d-flex justify-content-center">
                {{ $posts->links() }}
            </div>
        </div>
    </div>
</x-layout>
