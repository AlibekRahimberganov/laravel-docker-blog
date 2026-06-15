<x-layout>
    <div class="flex justify-between items-center my-8">
        <h1 class="text-3xl font-bold text-gray-800">Post Statistics</h1>
        <a href="{{ route('blog.stats.export') }}" class="btn bg-blue-500 text-white hover:bg-blue-600">
            <i class="fas fa-file-csv mr-2"></i>Export to CSV
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
        <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
            <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Views</p>
            <p class="text-3xl font-bold text-blue-600 mt-2">{{ number_format($totalViews) }}</p>
        </div>
        
        <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm md:col-span-2">
            <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Top Performing Post</p>
            @if($topPost)
                <p class="text-xl font-bold text-gray-800 mt-2 truncate">{{ $topPost->title }}</p>
                <p class="text-sm text-gray-500 mt-1">{{ $topPost->views_count }} views | {{ $topPost->likes_count }} likes</p>
            @else
                <p class="text-xl font-bold text-gray-400 mt-2">No posts yet</p>
            @endif
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Post Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider text-center">Views</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider text-center">Likes</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider text-center">Dislikes</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider text-center">Recs</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($posts as $post)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900">{{ $post->title }}</div>
                        <div class="text-xs text-gray-500">{{ $post->created_at->format('d M Y') }}</div>
                    </td>
                    <td class="px-6 py-4 text-center text-sm text-gray-500 font-semibold">{{ $post->views_count }}</td>
                    <td class="px-6 py-4 text-center text-sm text-blue-600">{{ $post->likes_count }}</td>
                    <td class="px-6 py-4 text-center text-sm text-red-600">{{ $post->dislikes_count }}</td>
                    <td class="px-6 py-4 text-center text-sm text-yellow-500">{{ $post->recommends_count }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                        You haven't published any posts yet.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-8">
        <a href="{{ route('blog.profile') }}" class="reg_btn">
            <i class="fas fa-arrow-left mr-1"></i> Back to Profile
        </a>
    </div>
</x-layout>
