<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $comment = Comment::create([
            'user_id' => Auth::id(),
            'post_id' => $request->post_id,
            'author' => $request->author,
            'content' => $request->content,
        ]);

        if($comment) {
            return response()->json([
                'comment' => $comment,
                'success' => true,
                'message' => 'Comment added.'
            ], 200);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function show(Comment $comment) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Comment $comment) {
        $the_comment = $comment->update([
            'content' => $request->content
        ]);

        $updated_comment = Comment::find($comment->id);

        if($the_comment) {
            return response()->json([
                'comment' => $updated_comment,
                'success' => true,
            ], 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Comment $comment) {
        if($comment->delete()) {
            return response()->json([
                'success' => true,
            ]);
        }
    }
}
