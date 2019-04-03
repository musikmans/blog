<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class like extends Model
{
    //
    public function Liked_Post() 
    {
        return $this->belongsTo('App\Post', 'post_id', 'id');
    }
}
