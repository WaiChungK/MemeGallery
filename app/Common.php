<?php

namespace App;

class Common {

	// Here list down all the constant values
	
	//
	
	const _int = [

		"comment_pp" => 10, // Displays 10 comments per page

	];

	//
	
	const _char = [
	
		// Value that will be stored in the 'status' of the PostLike table
		'like'		=> '+', 
		'dislike'	=> '-',
		
	];
	
	//
	
	const _desc = [
		
		'media'	=> [
			'image'	=> "Image",
			'gif'	=> "GIF",
			'video'	=> "Video",
		],
		
		'media_credentials'	=> [
			'image'	=> "Only supports jpeg, jpg and png. Maximum 2MB",
			'gif'	=> "Only supports gif. Maximum 2MB",
			'video'	=> "Only supports ogg, mp3 and mp4. Maximum 2MB",
		],
		
		'media_credentials_accept'	=> [
			'image'	=> "image/jpg, image/jpeg, image/png",
			'gif'	=> "image/gif",
			'video'	=> "video/mp4, video/webm, video/wmv, video/ogg",
		],
		
	];

	// Define the maximum length of the inputs

	const _len = [

		'post'	=> [
			'title'	=> 100,
			'description'	=> 2000,
		],

		'user'	=> [
			'name'	=> 255,
			'passwprd'	=> 255,
		],

		'postComment'	=> [
			'message'	=> 2000,
		],

	];
	
	//

	const image_interface_path = "image/interface/"; 
	const image_post_path = "image/post/"; 
	
	public static function GetImage() {
		return [
			'like_icon'		=> asset(Common::image_interface_path . 'like_icon.png'),		
			'dislike_icon'	=> asset(Common::image_interface_path . 'dislike_icon.png'),
		];
	}
	
}
