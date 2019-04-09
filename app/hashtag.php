<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class hashtag extends Model
{
    //

    protected $fillable = ['hashtag'];
    public function Hashtags() 
    {
        return $this->belongsToMany('App\Hashtag', 'posts_hashtags', 'post_id', 'hashtag_id');
    }
}
