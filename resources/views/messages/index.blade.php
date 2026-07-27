<x-layout>
    <h1 class="text-3xl font-bold my-8">Messages</h1>

    <div class="grid grid-cols-1 gap-3">
        @forelse ($conversations as $conversation)
            <a href="{{ route('messages.show', $conversation['friend']) }}" class="card p-4 flex items-center gap-4">
                <img src="{{ $conversation['friend']->avatar_url }}" alt="{{ $conversation['friend']->login }}" class="w-12 h-12 rounded-full object-cover">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                        <span class="font-semibold">{{ $conversation['friend']->login }}</span>
                        @if ($conversation['unread_count'] > 0)
                            <span class="bg-red-500 text-white text-xs rounded-full px-2 py-0.5">{{ $conversation['unread_count'] }}</span>
                        @endif
                    </div>
                    @if ($conversation['last_message'])
                        <p class="text-gray-500 text-sm truncate">{{ $conversation['last_message']->body }}</p>
                    @else
                        <p class="text-gray-400 text-sm italic">No messages yet — say hi!</p>
                    @endif
                </div>
            </a>
        @empty
            <p class="text-gray-500">You have no friends to message yet. Add some friends from their profile page first.</p>
        @endforelse
    </div>
</x-layout>
