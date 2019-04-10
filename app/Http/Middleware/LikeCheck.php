<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Post;

class LikeCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($this->validate($request)){
            $message[] = "You cannot vote more than once for the same article";
            return response()->json($message, 422);
        };
        return $next($request);
    }

    public function validate($request) {
        $userId = auth('api')->user()->id;
        $postId = $request->post->id;
        // check if the user has vote already
        $votes = \App\Like::where('post_id', $postId)
        ->where('user_id', $userId)
        ->first();
        if (is_object($votes))
        {
            return true;   
        }
    }
}
