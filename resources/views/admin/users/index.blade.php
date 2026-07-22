<x-layout>
    <div class="flex justify-between items-center my-8">
        <h1 class="text-3xl font-bold text-gray-800">Manage Users</h1>
        <a href="{{ route('admin.dashboard') }}" class="reg_btn"><i class="fas fa-arrow-left mr-1"></i> Back to Dashboard</a>
    </div>

    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Login</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider text-center">Posts</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider text-center">Role</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach ($users as $user)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $user->login }}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $user->email }}</td>
                    <td class="px-6 py-4 text-center text-sm text-gray-500">{{ $user->posts_count }}</td>
                    <td class="px-6 py-4 text-center text-sm">
                        <span class="px-2 py-1 rounded text-xs font-semibold {{ $user->isAdmin() ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600' }}">
                            {{ $user->role }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right text-sm">
                        @if ($user->id !== auth()->id())
                            <form action="{{ route('admin.users.role', $user) }}" method="POST" class="inline-block mr-2">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="role" value="{{ $user->isAdmin() ? 'user' : 'admin' }}">
                                <button type="submit" class="reg_btn">
                                    {{ $user->isAdmin() ? 'Demote' : 'Make Admin' }}
                                </button>
                            </form>
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline-block" onsubmit="return confirm('Delete this user and all their posts?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Delete</button>
                            </form>
                        @else
                            <span class="muted">(you)</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-8">
        {{ $users->links() }}
    </div>
</x-layout>
