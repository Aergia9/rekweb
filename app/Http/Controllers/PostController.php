<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Caching query posts selama 10 menit (600 detik)
        $posts = Cache::remember('posts', 600, function () {
            return Post::latest()->paginate(6);
        });

        return inertia('Home', ['posts' => $posts]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return inertia('Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        sleep(2);

        $fields = $request->validate([
            'body' => ['required']
        ]);

        Post::create($fields);

        // Hapus cache setelah post baru dibuat
        Cache::forget('posts');

        return redirect('/')->with(
            'success',
            'Post berhasil di buat'
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        // Caching data post individual selama 10 menit
        $cachedPost = Cache::remember('post_' . $post->id, 600, function () use ($post) {
            return $post;
        });

        return inertia('Show', ['post' => $cachedPost]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        return inertia('Edit', ['post' => $post]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        sleep(1);

        $fields = $request->validate([
            'body' => ['required']
        ]);

        $post->update($fields);

        // Hapus cache setelah post diupdate
        Cache::forget('posts');
        Cache::forget('post_' . $post->id);

        return redirect('/')->with(
            'success',
            'Post berhasil di edit'
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        $post->delete();

        // Hapus cache setelah post dihapus
        Cache::forget('posts');
        Cache::forget('post_' . $post->id);

        return redirect('/')->with(
            'message',
            'Post berhasil di hapus'
        );
    }
}
