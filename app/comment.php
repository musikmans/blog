<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class comment extends Model
{
    //
    public function Commented_Post() 
    {
        return $this->belongsTo('App\Post', 'post_id', 'id');
    }

    public function Commented_User() 
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }
}
