<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
		'user_id',
		'title',
		'description',
		'view',
		'media',
		'image_path',
	];
	
	public function mediaSource()
	{
		return asset(Common::image_post_path . $this->image_path);
	}

	public function user()
	{
		return $this->belongsTo("App\User");
	}
	
	public function comments()
	{
		return $this->hasMany("App\PostComment");
	}
	
	public function likes()
	{
		return $this->hasMany("App\PostLike");
	}
	
	public function likeCount()
	{
		return $this->likes()->where('status', Common::_char['like'])->count();
	}
	
	public function dislikeCount()
	{
		return $this->likes()->where('status', Common::_char['dislike'])->count();
	}

	//
	// Code was retrieved from
	// https://stackoverflow.com/questions/14174070/automatically-deleting-related-rows-in-laravel-eloquent-orm
	// Retrieved at 27 MAR 2020 
	public static function boot(){
		parent::boot();

		static::deleting(function($post){
			$post->comments()->delete();
			$post->likes()->delete();
		});
	}
}
