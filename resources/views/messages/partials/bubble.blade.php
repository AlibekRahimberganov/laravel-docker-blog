@php $isMine = $message->sender_id === auth()->id(); @endphp
<div class="flex {{ $isMine ? 'justify-end' : 'justify-start' }}">
    <div class="max-w-[70%] px-4 py-2 {{ $isMine ? 'bg-blue-500 text-white rounded-2xl rounded-br-md' : 'bg-gray-200 text-gray-900 rounded-2xl rounded-bl-md' }}"
        title="{{ $message->created_at->format('d M Y H:i') }}">
        <p class="text-sm leading-snug break-words" style="white-space: pre-wrap;">{{ $message->body }}</p>
    </div>
</div>
