<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $name = time() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('images', $name, 'public');
            $validated['image'] = $name;
        }

        $post = Post::create($validated);

        return response()->json($post, 201);
    }

    public function update(Request $request, Post $post)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);


        if ($request->hasFile('image')) {

            if ($post->image) {
                Storage::disk('public')->delete('images/' . $post->image);
            }

            $image = $request->file('image');
            $name = time() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('images', $name, 'public');
            $validated['image'] = $name;
        }

        $post->update($validated);

        return response()->json($post, 201);
    }


    public function delete($id)
    {
        $post = Post::findOrFail($id);

        $post->delete();

        if ($post->image) {
            Storage::disk('public')->delete('images/' . $post->image);
        }


        return response()->json(null, 204);
    }
}
