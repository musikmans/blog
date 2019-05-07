<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\User as User;

class UserController extends Controller
{
    //
    public function current(Request $request)
    { 
        $token = $request->header('Authorization');
        $token = substr($token, 7);
        if (!$token==""){
            $currentuser = \App\User::where('api_token', $token)->first();
        }
        if ($currentuser->isAdmin===1){
            $currentuser['canPostBlog']=1;
        } else {
            $currentuser['canPostBlog']=0;
        }
        return response()->json($currentuser, 200);
    }
}
