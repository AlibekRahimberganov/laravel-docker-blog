<?php

namespace App\Http\Controllers;

use App\Models\Posts;
use App\Services\MediaMetadataService;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BlogController extends Controller
{
    public function __construct(private MediaMetadataService $mediaMetadataService) {}

    public function store(Request $request)
    {
        /* Storing new post to database */
        $validated = $request->validate([
            'title' => 'required|string|min:5|max:50',
            'content_media' => 'nullable|file|mimes:jpg,jpeg,png,mp4,mp3|max:2097152',
            'content' => 'required|string|min:10|max:10000',
            'category_id' => 'required|exists:categories,id',
        ]);

        if ($request->hasFile('content_media')) {
            $validated = array_merge($validated, $this->storeMediaWithMetadata($request->file('content_media')));
        }
        $validated['published_at'] = now();

        $request->user()->posts()->create($validated);

        return redirect()->route('blog.home')->with('success', 'Post Created!');
    }

    // delete is working
    public function delete(Posts $post)
    {
        if (Auth::user()->id !== $post->user_id) {
            abort(403);
        }
        $post->delete();

        return redirect()->route('blog.home')->with('success', 'Post Deleted!');
    }

    public function edit(Posts $post)
    {
        if (Auth::user()->id !== $post->user_id) {
            abort(403);
        }

        return view('edit', ['post' => $post]);
    }

    public function update(Request $request, Posts $post)
    {
        if (Auth::user()->id !== $post->user_id) {
            abort(403);
        }
        $validated = $request->validate([
            'title' => 'required|string|min:5|max:50',
            'content_media' => 'nullable|file|mimes:jpg,jpeg,png,mp4,mp3|max:10240',
            'content' => 'required|string|min:10|max:10000',
        ]);

        if ($request->hasFile('content_media')) {
            $validated = array_merge($validated, $this->storeMediaWithMetadata($request->file('content_media')));
        }

        $post->update($validated);

        return redirect()->route('blog.home')->with('success', 'Post Updated!');
    }

    private function storeMediaWithMetadata(UploadedFile $file): array
    {
        $path = $file->store('media', 'public');
        $fields = [
            'content_media' => $path,
            'content_media_artist' => null,
            'content_media_cover' => null,
        ];

        if (strtolower($file->getClientOriginalExtension()) === 'mp3') {
            $metadata = $this->mediaMetadataService->extractAudioMetadata(Storage::disk('public')->path($path));
            $fields['content_media_artist'] = $metadata['artist'];
            $fields['content_media_cover'] = $metadata['cover_path'];
        }

        return $fields;
    }
}
