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

        return response()->json($currentuser, 200);
    }
}
