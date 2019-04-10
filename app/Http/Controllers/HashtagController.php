<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HashtagController extends Controller
{
    // Show all the save hashtag in the system
    public function index()
    {
        $hashtags=\App\Hashtag::orderBy('id')
        ->select('hashtag')
        ->get();
        return response()->json($hashtags, 200);
    }

    // search article with a given hashtag
    public function search(Request $request)
    {
        $search=$request->hashtag;
        $posts=\App\Post::orderBy('updated_at', 'desc')
        ->join('posts_hashtags', 'posts.id', '=', 'posts_hashtags.post_id')
        ->join('hashtags', 'posts_hashtags.hashtag_id', '=', 'hashtags.id')
        ->join('users', 'posts.user_id', '=', 'users.id')
        ->where('hashtags.hashtag', $search)
        ->select('posts.id', 'posts.title', 'posts.content', 'posts.created_at', 'posts.updated_at', 'users.name as author')
        ->get();
        return response()->json($posts, 200);
    }
}
