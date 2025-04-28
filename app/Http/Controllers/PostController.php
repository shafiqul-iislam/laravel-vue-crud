<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        return Post::latest()->paginate(5);
    }


    public function add(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $post = Post::create($validated);

        return response()->json($post, 201);
    }

    public function update(Request $request, Post $post)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $post->update($validated);

        return response()->json($post, 201);
    }


    public function delete($id)
    {
        Post::findOrFail($id)->delete();

        return response()->json(null, 204);
    }
}
