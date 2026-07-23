<x-layout>
    <h1>Edit Profile</h1>
    <form action="{{ route('blog.profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="flex items-center gap-4 mb-4">
            <img src="{{ $user->avatar_url }}" alt="{{ $user->login }}" class="w-24 h-24 rounded-full object-cover">
            <div class="flex-1">
                <label for="avatar">Profile picture:</label>
                <input type="file" id="avatar" name="avatar" accept="image/jpeg,image/png,image/webp">
                @if ($user->avatar)
                    <label class="form-check-inline font-normal">
                        <input type="checkbox" name="remove_avatar" value="1">
                        Remove current picture
                    </label>
                @endif
            </div>
        </div>

        <label for="bio">Bio:</label>
        <textarea id="bio" name="bio" placeholder="Tell others about yourself" rows="4" maxlength="1000">{{ old('bio', $user->bio) }}</textarea>

        <button class="btn" type="submit">Save</button>

        @if ($errors->any())
            <ul class="px-4 py-2 bg-red-100">
                @foreach ($errors->all() as $error)
                    <li class="my-2 text-red-500">{{ $error }}</li>
                @endforeach
            </ul>
        @endif
    </form>
</x-layout>
