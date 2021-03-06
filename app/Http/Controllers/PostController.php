<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
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
    }

    public function show(Request $request, Post $post)
    {   
        // checking if a user is currenttly logged and getting his id
        // in order to determine if he can edit post or a comment or he's an admin
        $token = $request->header('Api-Token');
        $token = substr($token, 7);
        $currentUserId = 0;
        $isCurrentUserAdmin = 0;
        if (!$token==""){
            $currentuser = \App\User::where('api_token', $token)->first();
            if (is_object($currentuser)) { 
                $currentUserId = $currentuser->id;
                $isCurrentUserAdmin = $currentuser->isAdmin;
            }
        }
        $user_instance = new User();
        $post_id = $post['id'];
        $userId = $post['user_id'];
        $user = $user_instance->find($userId);
        $post['author'] = $user->name;
        if ($currentUserId===$userId) {
        $post['authorIsCurrentUser'] = true;
        } else {
        $post['authorIsCurrentUser'] = false;
        }
        // Getting article score
        $post['currentUserScore'] = 0;
        $post['points'] = \App\Like::where('post_id', $post_id)->sum('score');
        $currentUserScore = \App\Like::where('post_id', $post_id)
        ->where('user_id', $currentUserId)
        ->first();
        print_r($currentUserScore);
        if (is_object($currentUserScore)) { 
            $post['currentUserScore'] = $currentUserScore->score;
        }

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
            // only comments owner or admin (to moderate) can edit or delete the comment
            if ($currentUserId===$userId || $isCurrentUserAdmin===1) {
                $bool = true;
            } else {
                $bool = false;
            }
            $comments_lists[] = $array = array(
                "id"  => $comment['id'],
                "author"  => $user->name,
                "body" => $comment['body'],
                "created_at" => $comment['created_at'],
                "updated_at" => $comment['updated_at'],
                "commentsCanBeEdited" => $bool,
            );
        }
        $post['comments'] = $comments_lists;
        return response()->json($post, 200);
    }

    public function store(Request $request)
    {
        $userId = auth('api')->user()->id;
        $user = \App\User::where('id', $userId)->first();
        $isAdmin = $user->isAdmin;
        $name = $user->name;
        if ($isAdmin){
            $validatedData = $request->validate([
                'title' => 'required|unique:posts|min:255',
                'content' => 'required',
                'hashtags' => 'required',
            ]);
            if (isset($errors)){
                return response()->json($errors, 422); 
            } else {
                $post = Post::create(['title' => $request->title, 'content' => $request->content, 'user_id' => $userId, 'updated_at' => date("Y-m-d H:i:s"), 'created_at' => date("Y-m-d H:i:s")]);
                $postId = $post->id;
                $hashtagsArray=explode(',', $request->hashtags);
                foreach ($hashtagsArray as $hashtag){
                    // remove empty spaces
                    $hashtag = preg_replace('/\s+/', '', $hashtag);
                    $hashtag = strtolower($hashtag);
                    // Will create hashtags that doesn't exist
                    $tags = \App\Hashtag::firstOrCreate(['hashtag' => $hashtag]);
                    // Update reference table
                    $hashtagId=$tags->id;
                    $hashpost = DB::table('posts_hashtags')->insert(
                        ['post_id' => $postId, 'hashtag_id' => $hashtagId]
                    );
                }
                // add hashtags to the post object
                $tags = \App\Hashtag::find($postId);
                $hashes = [];
                foreach ($tags->Hashtags as $hashtag){
                    $hashes[]=$hashtag->hashtag;
                }
                $post['hashtags']=$hashes;
                $post['author']=$name;
                return response()->json($post, 201);     
            }
        } else {
            $message[] = "Unauthorized access, only admin can post blog";
            return response()->json($message, 403);
        }
    }

    public function update(Request $request, Post $post)
    {
        // check if the person own the post or not
        $userId = auth('api')->user()->id;
        $user = \App\User::where('id', $userId)->first();
        $name = $user->name;
        $postUserId = $post['user_id'];
        $postId = $post['id'];
        if ($userId === $postUserId){
            $request->validate([
                'title' => 'required|unique:posts|min:255',
                'content' => 'required',
                'hashtags' => 'required',
            ]);
            if (isset($errors)){
                return response()->json($errors, 422); 
            } else {
            // user can edit 
            // get all hashtag store for that post
            $tags = \App\Hashtag::find($postId);
            $current_hashtags = [];
            foreach ($tags->Hashtags as $hashtag){
                $current_hashtags[]=$hashtag->hashtag;
            }
            // Hashes in the current edit
            $new_hashtags = explode(',', preg_replace('/\s+/', '', strtolower($request->hashtags)));
            $result_to_delete = array_diff($current_hashtags, $new_hashtags);
            $result_to_add = array_diff($new_hashtags, $current_hashtags);
            // delete reference to hashtag
            foreach ($result_to_delete as $destroy){
                echo $destroy;
                $posts = DB::table('posts_hashtags')
                ->join('hashtags', 'posts_hashtags.hashtag_id', '=', 'hashtags.id')
                ->where('hashtags.hashtag', $destroy)
                ->where('posts_hashtags.post_id', $postId)
                ->delete();
            }
            // add new hashtags to list
            foreach ($result_to_add as $add){
                $tags = \App\Hashtag::firstOrCreate(['hashtag' => $add]);
                // Update reference table
                $hashtagId=$tags->id;
                $hashpost = DB::table('posts_hashtags')->insert(
                    ['post_id' => $postId, 'hashtag_id' => $hashtagId]
                );
            }
            $post->update(['title' => $request->title, 'content' => $request->content, 'user_id' => $userId, 'updated_at' => date("Y-m-d H:i:s")]);

            // add hashtags to the post object
            $tags = \App\Hashtag::find($postId);
            $hashes = [];
            foreach ($tags->Hashtags as $hashtag){
                $hashes[]=$hashtag->hashtag;
            }
            $post['hashtags']=$hashes;
            $post['author']=$name;

            return response()->json($post, 200);
            }
        } else {
            $message[] = "Unauthorized access, only the owner of the post can edit";
            return response()->json($message, 403);
        }
    }

    public function delete(Request $request, Post $post)
    {   
        $userId = auth('api')->user()->id;
        if ($post['user_id']===$userId)
        {
            $post->delete();
            return response()->json(null, 204);
        } else {
            $message[] = "Unauthorized access, only the owner of the post can delete";
            return response()->json($message, 403);
        }
    }
}
