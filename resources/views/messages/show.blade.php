<x-layout>
    <div class="flex flex-col" style="height: calc(100vh - 260px); min-height: 420px;">
        <div class="flex items-center gap-3 pb-4 border-b border-gray-200">
            <img src="{{ $friend->avatar_url }}" alt="{{ $friend->login }}" class="w-11 h-11 rounded-full object-cover">
            <h1 class="text-lg font-semibold">{{ $friend->login }}</h1>
            <a href="{{ route('messages.index') }}" class="reg_btn ml-auto text-sm">Back to Messages</a>
        </div>

        <div id="message-list" class="flex-1 overflow-y-auto flex flex-col gap-2 py-4">
            @forelse ($messages as $message)
                @include('messages.partials.bubble', ['message' => $message])
            @empty
                <p class="text-gray-400 text-center m-auto">No messages yet — say hi!</p>
            @endforelse
        </div>

        <form id="message-form" action="{{ route('messages.store', $friend) }}" method="POST"
            class="flex items-end gap-2 pt-3 border-t border-gray-200">
            @csrf
            <textarea name="body" id="message-input" placeholder="Message..." rows="1" maxlength="2000" required
                style="resize: none;"
                class="!mt-0 !mb-0 !border-gray-300 flex-1 text-sm rounded-2xl px-4 py-2 leading-normal max-h-[100px] overflow-y-auto"></textarea>
            <button type="submit" id="message-send" class="w-10 h-10 shrink-0 rounded-full bg-blue-500 text-white flex items-center justify-center hover:bg-blue-600 transition disabled:opacity-50" title="Send">
                <i class="fas fa-paper-plane"></i>
            </button>
        </form>

        @if ($errors->any())
            <ul class="px-4 py-2 bg-red-100 mt-2">
                @foreach ($errors->all() as $error)
                    <li class="my-2 text-red-500">{{ $error }}</li>
                @endforeach
            </ul>
        @endif
    </div>
</x-layout>
