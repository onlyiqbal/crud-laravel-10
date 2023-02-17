<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

        dd($request);

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
}
