<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Post extends Model
{
    //
    protected $fillable = ['title', 'content', 'hashtags', 'user_id'];
    protected $hidden = [
        'password', 'user_id',
    ];

    public function User() 
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

    public function Comments()
    {
        return $this->hasMany('App\Comment');
    }

    public function Hashtags()
    {
        return $this->hasMany('App\Hashtag');
    }

    public function Points()
    {
        return $this->hasMany('App\Like');
    }
}