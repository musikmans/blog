<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;
use App\Like;

class LikeController extends Controller
{
    //
    public function create(Request $request, Post $post)
    {
        $userId = auth('api')->user()->id;
        $postId = $post->id;
        $score = $request->score;
        // add the votes
        $votes = new Like;
        $votes->user_id = $userId;
        $votes->post_id = $postId;
        $votes->score = $score;
        $votes->save();
        // return new score
        $getScore = \App\Like::where('post_id', $postId)->sum('score');
        $content['score']=$getScore;
        return response()->json($content, 200);
    }

    public function delete(Request $request, Post $post)
    {
        $userId = auth('api')->user()->id;
        $postId = $post->id;
        $deleteScore = \App\Like::where('post_id', $postId)
        ->where('user_id', $userId)
        ->delete();
        // return new score
        $getScore = \App\Like::where('post_id', $postId)->sum('score');
        $content['score']=$getScore;
        return response()->json($content, 200);
    }
}
