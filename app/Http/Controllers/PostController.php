<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;
use App\User as User;

class PostController extends Controller
{
    //
    public function index()
    {
        $posts=Post::orderBy('updated_at', 'desc')->get();
        $user_instance = new User();
        foreach ($posts as $post){
            $userId = $post['user_id'];
            $user = $user_instance->find($userId);
            $post['author'] = $user->name;
        }
        return response()->json($posts, 200);

        /* Another way without using eloquent
        $posts = DB::table('posts')
            ->join('users', 'posts.user_id', '=', 'users.id')
            ->select('posts.id', 'title', 'content', 'name as author','posts.created_at', 'posts.updated_at')
            ->orderBy('posts.updated_at', 'desc')
            ->get();
        return $posts;
        */
    }

    public function show(Post $post)
    {   
        $user_instance = new User();
        $post_id = $post['id'];
        $userId = $post['user_id'];
        $user = $user_instance->find($userId);
        $post['author'] = $user->name;
        // Getting article score
        $scores = \App\Post::find($post_id)->points;
        $points = [];
        foreach ($scores as $pointage){
            $points[]=$pointage->score;
        }
        $post['points']=array_sum($points);
        // Sorting Hashtags
        $tags = \App\Hashtag::find($post_id);
        $hashes = [];
        foreach ($tags->Hashtags as $hashtag){
            $hashes[]=$hashtag->hashtag;
        }
        $post['hashtags']=$hashes;
        // Sorting Comments
        $comments_lists = [];
        $comments = \App\Post::find($post_id)->comments;
        $comments = $comments->sortByDesc('updated_at');
        foreach ($comments as $comment) {
            $user_instance = new User();
            $userId = $comment['user_id'];
            $user = $user_instance->find($userId);
            $user->name;
            $comments_lists[] = $array = array(
                "author"  => $user->name,
                "body" => $comment['body'],
                "created_at" => $comment['created_at'],
                "updated_at" => $comment['updated_at'],
            );
        }
        $post['comments'] = $comments_lists;
        return response()->json($post, 200);
    }

    public function store(Request $request)
    {
        $token = $request->api_token;
        $user = \App\User::where('api_token', $token)->first();
        $userId = $user->id;
        $isAdmin = $user->isAdmin;
        if ($isAdmin){
            $post = Post::create($request->all() + ['user_id' => $userId]);
            return response()->json($post, 201);
        } else {
            $message[] = "Unauthorized access, only admin can post blog";
            return response()->json($message, 403);
        }
    }

    public function update(Request $request, Post $post)
    {
        $post->update($request->all());

        return response()->json($post, 200);
    }

    public function delete(Post $post)
    {
        $post->delete();

        return response()->json(null, 204);
    }
}
