<x-layout>
    <h1>Edit Category</h1>
    <form action="{{ route('admin.categories.update', $category) }}" method="POST">
        @csrf
        @method('PUT')
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" value="{{ old('name', $category->name) }}" maxlength="255" required>
        <label for="description">Description:</label>
        <textarea id="description" name="description" rows="4" maxlength="1000">{{ old('description', $category->description) }}</textarea>
        <button class="btn" type="submit">Update Category</button>

        @if($errors->any())
            <ul class="px-4 py-2 bg-red-100">
                @foreach($errors->all() as $error)
                    <li class="my-2 text-red-500">{{ $error }}</li>
                @endforeach
            </ul>
        @endif
    </form>
</x-layout>
