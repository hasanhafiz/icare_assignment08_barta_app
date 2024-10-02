<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\Post;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::with(['user', 'tags'])->orderBy('created_at', 'DESC')->get();
        return view('posts.index', compact('posts'));
    }
    
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request)
    {
        
        // Validate the request
        $validated = $request->validated();
                
        // dd( $request->all() );
        $post = Post::create($validated);
        
        // Extract tags from the content (assumes tags are prefixed by #)
        preg_match_all('/#(\w+)/', $request->input('body'), $matches);
        $tags = $matches[1];
        
        // Attach tags to the post
        foreach ($tags as $tag_name) {
            // First or create the tag
            $tag = Tag::firstOrCreate(['tag_name' => $tag_name]);
            // Attach the tag to the post
            $post->tags()->attach($tag);
        }
        
        $posts = Post::with(['user', 'tags'])->orderBy('created_at', 'DESC')->get();                
        return route('posts.index', ['posts' => $posts]);
    }
    
    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        return view('posts.edit', compact('post'));
    }
    
    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, Post $post)
    {
        // Validate the request
        $validated = $request->validated();
                        
        // dd( $request->all() );
        $post->update( $validated );
        
        // Extract tags from the content (assumes tags are prefixed by #)
        preg_match_all('/#(\w+)/', $request->input('body'), $matches);
        $tags = $matches[1];
        
        // Attach tags to the post
        foreach ($tags as $tag_name) {
            // First or create the tag
            $tag = Tag::firstOrCreate(['tag_name' => $tag_name]);
            // Attach the tag to the post
            $post->tags()->attach($tag);
        }

        $posts = Post::with(['user', 'tags'])->orderBy('created_at', 'DESC')->get();
        return route('posts.index', ['posts' => $posts]);        
    }
    
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        $post->delete();
        return to_route('posts.index');
    }
}
