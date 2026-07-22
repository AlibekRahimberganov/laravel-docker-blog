<x-layout>
    <div class="flex justify-between items-center my-8">
        <h1 class="text-3xl font-bold text-gray-800">Admin Dashboard</h1>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-12">
        <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
            <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Users</p>
            <p class="text-3xl font-bold text-gray-800 mt-2">{{ number_format($stats['users_count']) }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
            <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Posts</p>
            <p class="text-3xl font-bold text-gray-800 mt-2">{{ number_format($stats['posts_count']) }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
            <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Categories</p>
            <p class="text-3xl font-bold text-gray-800 mt-2">{{ number_format($stats['categories_count']) }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
            <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Views</p>
            <p class="text-3xl font-bold text-blue-600 mt-2">{{ number_format($stats['views_count']) }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
            <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Likes</p>
            <p class="text-3xl font-bold text-blue-600 mt-2">{{ number_format($stats['likes_count']) }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
            <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Dislikes</p>
            <p class="text-3xl font-bold text-red-600 mt-2">{{ number_format($stats['dislikes_count']) }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
            <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Recommends</p>
            <p class="text-3xl font-bold text-yellow-500 mt-2">{{ number_format($stats['recommends_count']) }}</p>
        </div>
    </div>

    <div class="flex gap-4">
        <a href="{{ route('admin.users.index') }}" class="btn">Manage Users</a>
        <a href="{{ route('admin.posts.index') }}" class="btn">Manage Posts</a>
        <a href="{{ route('admin.categories.index') }}" class="btn">Manage Categories</a>
    </div>
</x-layout>
