<?php

namespace App\Services;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Cache;

class PostService
{
    /**
     * Get paginated posts for the authenticated user.
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function index()
    {
        //Use caching to remember the authenticated user's posts excluding banned ones.
        return Cache::rememberForever(auth()->id() . '_posts', function () {
            return auth()->user()->posts()->notBanned()->paginate(10);
        });
    }

    /**
     * Create and return a new post with provided data.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     */
    public function store($request)
    {
        $imageName = null;
        $imagePath = null;
        //Get image name and path from request
        if($request->file('image')) {
            $imageName = $request->file('image')->getClientOriginalName();
            $imagePath = "storage/" . $request->file('image')
                    ->store('posts-images');
        }
        //Create new post with data
        $postData = $request->validated();
        $postData['user_id'] = auth()->id();
        $postData['image_name'] = $imageName;
        $postData['image_path'] = $imagePath;

        return Post::query()->create($postData);
    }

    /**
     * Get a specific post with comments and likes, excluding banned posts.
     *
     * @param \App\Models\Post $post
     * @return \App\Models\Post
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function show($post)
    {
        // Retrieve and return a specific post with comments, likes, and excluding banned posts.
        return Post::with('comments', 'likes')
            ->notBanned()
            ->findOrFail($post->id);
    }

    /**
     * Update a specific post's content and image.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Post $post
     * @return \App\Models\Post
     */
    public function update($request, $post)
    {
        // Validate the request
        $validatedData = $request->validated();

        // Update the post content
        $post->update([
            'content' => $validatedData['content'],
        ]);

        // Update the post image if a new image is provided
        if ($request->hasFile('image')) {
            $imageName = $request->file('image')->getClientOriginalName();
            $imagePath = "storage/" . $request->file('image')->store('posts-images');

            $post->update(['image_name' => $imageName, 'image_path' => $imagePath]);
        }

        return $post;
    }

    /**
     * Delete a specific post.
     *
     * @param \App\Models\Post $post
     * @return bool|null
     */
    public function destroy($post)
    {
        return $post->delete();
    }
}
