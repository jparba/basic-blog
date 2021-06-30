<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\AddPostRequest;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        if($_GET['mypost'] == 'true') {
            $post = Post::with('user', 'comment')->where('user_id', Auth::id())->orderBy('created_at', 'desc')->paginate(10);
        }else{
            $post = Post::with('user', 'comment')->orderBy('created_at', 'desc')->paginate(5);
        }
        return response()->json($post, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AddPostRequest $request) {
        $data = [
            'user_id' => Auth::user()->id,
            'slug' => $request->slug,
            'title' => $request->title,
            'content' => $request->content
        ];

        $post = Post::create($data);

        $post['user'] = User::findOrFail(Auth::user()->id);
        $post['comment'] = [];

        if($post->wasRecentlyCreated) {
            return response()->json([
                'post' => $post,
                'success' => true,
                'message' => 'Post added.'
            ], 201);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Adding post failed.'
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show($slug) {
        $post = Post::with('user', 'comment')->where('slug', $slug)->first();
        return response()->json($post, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(AddPostRequest $request, Post $post) {
        $post = $post->update($request->all());
        if($post) {
            return response()->json([
                'post'    => $request->all(),
                'success' => true,
                'message' => 'Post updated.'
            ]);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Updating post failed.'
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post) {
        if($post->delete()) {
            return response()->json([
                'success' => true,
                'message' => 'Post deleted.'
            ]);
        }
    }
}
