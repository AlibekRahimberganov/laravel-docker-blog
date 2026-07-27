<?php

namespace App\Http\Controllers;

use App\Models\Posts;
use App\Models\Tag;
use App\Models\User;
use App\Services\MediaMetadataService;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

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
            'tags' => 'nullable|string|max:200',
            'co_author_user' => 'nullable|array|max:5',
            'co_author_user.*' => 'nullable|integer',
            'co_author_name' => 'nullable|array|max:5',
            'co_author_name.*' => 'nullable|string|max:100',
        ]);

        if ($request->hasFile('content_media')) {
            $validated = array_merge($validated, $this->storeMediaWithMetadata($request->file('content_media')));
        }
        $validated['published_at'] = now();

        $tagsInput = $validated['tags'] ?? null;
        $coAuthorUsers = $validated['co_author_user'] ?? [];
        $coAuthorNames = $validated['co_author_name'] ?? [];
        unset($validated['tags'], $validated['co_author_user'], $validated['co_author_name']);

        DB::transaction(function () use ($request, $validated, $coAuthorUsers, $coAuthorNames, $tagsInput) {
            $post = $request->user()->posts()->create($validated);
            $this->syncCoAuthors($post, $coAuthorUsers, $coAuthorNames);
            $this->syncTags($post, $tagsInput);
        });

        return redirect()->route('blog.home')->with('success', 'Post Created!');
    }

    // delete is working
    public function delete(Posts $post)
    {
        if (! $post->isEditableBy(Auth::user())) {
            abort(403);
        }
        $post->delete();

        return redirect()->route('blog.home')->with('success', 'Post Deleted!');
    }

    public function edit(Posts $post)
    {
        if (! $post->isEditableBy(Auth::user())) {
            abort(403);
        }

        $post->load(['coAuthors', 'tags']);
        $users = User::where('id', '!=', Auth::id())->orderBy('login')->get();

        return view('edit', ['post' => $post, 'users' => $users]);
    }

    public function update(Request $request, Posts $post)
    {
        if (! $post->isEditableBy(Auth::user())) {
            abort(403);
        }
        $validated = $request->validate([
            'title' => 'required|string|min:5|max:50',
            'content_media' => 'nullable|file|mimes:jpg,jpeg,png,mp4,mp3|max:10240',
            'content' => 'required|string|min:10|max:10000',
            'tags' => 'nullable|string|max:200',
            'co_author_user' => 'nullable|array|max:5',
            'co_author_user.*' => 'nullable|integer',
            'co_author_name' => 'nullable|array|max:5',
            'co_author_name.*' => 'nullable|string|max:100',
        ]);

        if ($request->hasFile('content_media')) {
            $validated = array_merge($validated, $this->storeMediaWithMetadata($request->file('content_media')));
        }

        $tagsInput = $validated['tags'] ?? null;
        $coAuthorUsers = $validated['co_author_user'] ?? [];
        $coAuthorNames = $validated['co_author_name'] ?? [];
        unset($validated['tags'], $validated['co_author_user'], $validated['co_author_name']);

        DB::transaction(function () use ($post, $validated, $coAuthorUsers, $coAuthorNames, $tagsInput) {
            $post->update($validated);
            $this->syncCoAuthors($post, $coAuthorUsers, $coAuthorNames);
            $this->syncTags($post, $tagsInput);
        });

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

    /**
     * Each row is either a picked existing user id (co_author_user[i]) or a free-text
     * name (co_author_name[i]) — whichever is filled for a given index wins, with the
     * user pick taking priority if somehow both are filled for the same row.
     *
     * @param  array<int, int|string|null>  $userIds
     * @param  array<int, string|null>  $names
     */
    private function syncCoAuthors(Posts $post, array $userIds, array $names): void
    {
        $prepared = [];
        $seenUserIds = [];
        $rowCount = max(count($userIds), count($names));

        for ($index = 0; $index < $rowCount; $index++) {
            $userId = $userIds[$index] ?? null;
            $name = trim($names[$index] ?? '');

            if ($userId !== null && $userId !== '') {
                $userId = (int) $userId;
                if ($userId === $post->user_id) {
                    throw ValidationException::withMessages(['co_author_user' => 'The post owner does not need to be listed as a co-author.']);
                }
                if (! User::where('id', $userId)->exists()) {
                    throw ValidationException::withMessages(['co_author_user' => 'Selected co-author user does not exist.']);
                }
                if (in_array($userId, $seenUserIds, true)) {
                    throw ValidationException::withMessages(['co_author_user' => 'The same co-author was added more than once.']);
                }
                $seenUserIds[] = $userId;

                $prepared[] = ['user_id' => $userId, 'name' => null, 'display_order' => $index];
            } elseif ($name !== '') {
                $prepared[] = ['user_id' => null, 'name' => $name, 'display_order' => $index];
            }
        }

        $post->coAuthors()->delete();
        if ($prepared !== []) {
            $post->coAuthors()->createMany($prepared);
        }
    }

    private function syncTags(Posts $post, ?string $tagsInput): void
    {
        if (blank($tagsInput)) {
            $post->tags()->sync([]);

            return;
        }

        $names = collect(explode(',', $tagsInput))
            ->map(fn ($name) => trim($name))
            ->filter()
            ->unique(fn ($name) => Str::slug($name))
            ->values();

        if ($names->count() > 10) {
            throw ValidationException::withMessages(['tags' => 'A post can have at most 10 tags.']);
        }

        foreach ($names as $name) {
            if (mb_strlen($name) > 30) {
                throw ValidationException::withMessages(['tags' => "Tag \"$name\" exceeds the 30 character limit."]);
            }
        }

        $tagIds = $names->map(function ($name) {
            return Tag::firstOrCreate(['slug' => Str::slug($name)], ['name' => $name])->id;
        });

        $post->tags()->sync($tagIds);
    }
}
