<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable {

    protected $fillable = [
		'name',
		'email',
		'password',
    ];

    protected $hidden = [
        'password',
    ];
	
	public function posts()
	{
		return $this->hasMany("App\Post");
	}
	
	public function comments()
	{
		return $this->hasMany("App\PostComment");
	}
	
	public function likes()
	{
		return $this->hasMany("App\PostLike");
	}
	
}
