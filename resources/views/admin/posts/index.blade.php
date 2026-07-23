<x-layout>
    <div class="flex justify-between items-center my-8">
        <h1 class="text-3xl font-bold text-gray-800">Manage Posts</h1>
        <a href="{{ route('admin.dashboard') }}" class="reg_btn"><i class="fas fa-arrow-left mr-1"></i> Back to Dashboard</a>
    </div>

    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Author</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach ($posts as $post)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <a href="{{ route('blog.post', $post) }}" class="text-sm font-medium text-gray-900 hover:underline">{{ $post->title }}</a>
                        <div class="text-xs text-gray-500">{{ $post->created_at->format('d M Y') }}</div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        {{ $post->user->login }}
                        @if ($post->coAuthors->isNotEmpty())
                            <span class="text-xs text-gray-400">+{{ $post->coAuthors->count() }} co-author{{ $post->coAuthors->count() > 1 ? 's' : '' }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $post->category->name }}</td>
                    <td class="px-6 py-4 text-right text-sm">
                        <form action="{{ route('admin.posts.destroy', $post) }}" method="POST" class="inline-block" onsubmit="return confirm('Delete this post?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-8">
        {{ $posts->links() }}
    </div>
</x-layout>
