<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    //
    protected $fillable = ['title', 'content', 'user_id'];
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

    public function Points()
    {
        return $this->hasMany('App\Like');
    }
}