<x-layout>
    <h1>Edit</h1>
    <form action="{{ route('blog.update', $post->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <br>
        <label for="title">Enter title:</label>
        <input type="text" id="title" name="title" value="{{ old('title', $post->title) }}" placeholder="Title (max 50 characters)" maxlength="50" required><br>
        <label for="content_media">Upload media:</label>
        <input type="file" id="content_media" name="content_media" accept="image/*,video/*,audio/*"><br>
        <label for="content">Enter blog body:</label>
        <textarea id="content" name="content" placeholder="Blog Body" rows="15" maxlength="10000" required>{{ old('content', $post->content) }}</textarea><br>

        <label for="tags">Tags (comma-separated, up to 10):</label>
        <input type="text" id="tags" name="tags" value="{{ old('tags', $post->tags->pluck('name')->join(', ')) }}" placeholder="e.g. music, classical, history" maxlength="200"><br>

        <label>Co-authors (optional, up to 5):</label>
        @php $existingCoAuthors = $post->coAuthors->values(); @endphp
        @for ($i = 0; $i < 5; $i++)
            @php $coAuthor = $existingCoAuthors->get($i); @endphp
            <div class="flex gap-4 mb-2">
                <select name="co_author_user[]" class="flex-1">
                    <option value="">-- Not an existing user --</option>
                    @foreach ($users as $siteUser)
                        <option value="{{ $siteUser->id }}" {{ old("co_author_user.$i", $coAuthor?->user_id) == $siteUser->id ? 'selected' : '' }}>{{ $siteUser->login }}</option>
                    @endforeach
                </select>
                <input type="text" name="co_author_name[]" value="{{ old("co_author_name.$i", $coAuthor?->name) }}" placeholder="or free-text author name" class="flex-1">
            </div>
        @endfor

        <button class="btn" type="submit">Update Post</button>

        @if($errors->any())
            <ul class="px-4 py-2 bg-red-100">
                @foreach($errors->all() as $error)
                    <li class="my-2 text-red-500">{{ $error }}</li>
                @endforeach
            </ul>
        @endif
    </form>
</x-layout>
