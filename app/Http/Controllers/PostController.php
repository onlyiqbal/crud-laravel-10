<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PostController extends Controller
{
    public function index(): View
    {
        //get posts
        $posts = Post::latest()->paginate(5);

        return view('posts.index', compact('posts'));
    }

    public function create(): View
    {
        return view('posts.create');
    }

    public function store(Request $request): RedirectResponse
    {
        //validate form
        $this->validate($request, [
            'image' => 'required|image|mimes:png,jpg,jpeg|max:2048',
            'title' => 'required|min:5',
            'content' => 'required|min:10',
        ]);

        //upload image
        $image = $request->file('image');
        $image->storeAs('public/posts', $image->hashName());

        //create post
        Post::create([
            'image' => $image->hashName(),
            'title' => $request->input('title'),
            'content' => $request->input('content')
        ]);

        return redirect()->route('posts.index')->with(['success' => 'Data berhasil disimpan !']);
    }

    public function show(string $id): View
    {
        //get post bu id
        $post = Post::findOrFail($id);

        //render view with post
        return view('posts.show', compact('post'));
    }

    public function edit(string $id): View
    {
        //get post by id
        $post = Post::findOrFail($id);

        //render view with post
        return view('posts.edit', compact('post'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $this->validate($request, [
            'image' => 'image|mimes:png,jpg,jpeg|max:2048',
            'title' => 'required|min:5',
            'content' => 'required|min:10',
        ]);

        //get post by id
        $post = Post::findOrFail($id);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $image->storeAs('public/posts', $image->hashName());

            //delete old image
            Storage::delete('public/posts' . $post->image);

            //update post with image
            $post->update([
                'image' => $image->hashName(),
                'title' => $request->input('title'),
                'content' => $request->input('content'),
            ]);
        } else {
            $post->update([
                'title' => $request->input('title'),
                'content' => $request->input('content'),
            ]);
        }

        return redirect()->route('posts.index')->with(['success' => 'Data berhasil diupdate']);
    }

    public function destroy($id): RedirectResponse
    {
        //get post by id
        $post = Post::findOrFail($id);

        //delete image
        Storage::delete('public/posts/' . $post->image);

        $post->delete();

        return redirect()->route('posts.index')->with(['success' => 'Data berhasil dihapus!']);
    }
}
