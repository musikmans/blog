<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;
use App\Comment;
use App\User;

class CommentController extends Controller
{
    //
    public function create(Request $request, Post $post)
    {
        $currentUserId = auth('api')->user()->id;
        $user = \App\User::where('id', $currentUserId)->first();
        $isCurrentUserAdmin = $user->isAdmin;
        $validatedData = $request->validate([
            'body' => 'required|min:20',
        ]);
        if (isset($errors)){
            return response()->json($errors, 422); 
        }
        $postId = $post->id;
        $comments = new Comment;
        $comments->user_id = $currentUserId;
        $comments->post_id = $post->id;
        $comments->body = $request->body;
        $comments->save();

        return $this->comment_list($currentUserId, $isCurrentUserAdmin, $post->id);
    }

    public function edit(Request $request, Post $post)
    {
        $currentUserId = auth('api')->user()->id;
        $user = \App\User::where('id', $currentUserId)->first();
        $isCurrentUserAdmin = $user->isAdmin;
        $validatedData = $request->validate([
            'body' => 'required|min:20',
        ]);
        if (isset($errors)){
            return response()->json($errors, 422); 
        }
        $postId = $post->id;
        $commentId = $request->comment_id;
        $comment = Comment::where('id', '=', $commentId)->first();
        $comment->body = $request->body;
        $comment->save();

        return $this->comment_list($currentUserId, $isCurrentUserAdmin, $postId);
    }

    public function delete(Request $request, Post $post)
    {
        $currentUserId = auth('api')->user()->id;
        $user = \App\User::where('id', $currentUserId)->first();
        $isCurrentUserAdmin = $user->isAdmin;

        $postId = $post->id;
        $commentId = $request->comment_id;
        $comment = Comment::where('id', '=', $commentId)->first();
        $comment->delete();

        return $this->comment_list($currentUserId, $isCurrentUserAdmin, $postId);
    }

    public function comment_list($currentUserId, $isCurrentUserAdmin, $postid)
    {
        // Sorting Comments
        $comments_lists = [];
        $comments = \App\Post::find($postid)->comments;
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
        $repost['comments'] = $comments_lists;
        return response()->json($repost, 201); 
    }
}
