<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PostComment extends Model
{
    protected $fillable = [
		'user_id',
		'post_id',
		'message',
		'child_of',
	];
	
	public function post()
	{
		return $this->belongsTo("App\Post");
	}
	
	public function user()
	{
		return $this->belongsTo("App\User");
	}

	// Self reference - childrens

	public function replies()
	{
		return $this->hasMany('App\PostComment', 'child_of');
	}

	// Self reference - parent

	public function parentComment()
	{
		return $this->belongsTo('App\PostComment', 'post_id');
	}

	public function withUserInfo()
	{
		return $this->join('users', 'comments.user_id', '=', 'users.id');
	}
}
