<x-layout>
    <div class="flex justify-between items-center my-8">
        <h1 class="text-3xl font-bold text-gray-800">Manage Categories</h1>
        <div class="flex gap-4">
            <a href="{{ route('admin.categories.create') }}" class="btn">New Category</a>
            <a href="{{ route('admin.dashboard') }}" class="reg_btn"><i class="fas fa-arrow-left mr-1"></i> Back to Dashboard</a>
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider text-center">Posts</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach ($categories as $category)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $category->name }}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $category->description }}</td>
                    <td class="px-6 py-4 text-center text-sm text-gray-500">{{ $category->posts_count }}</td>
                    <td class="px-6 py-4 text-right text-sm">
                        <a href="{{ route('admin.categories.edit', $category) }}" class="reg_btn mr-2">Edit</a>
                        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline-block" onsubmit="return confirm('Delete this category?');">
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
</x-layout>
