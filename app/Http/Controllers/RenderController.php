<?php

namespace App\Http\Controllers;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use Illuminate\Http\Request;

use App\Post;
use App\PostComment;
use App\PostLike;

use App\Common;

class RenderController extends Controller
{
	// ----------------------------------------------------------------------------------
	
	// Return an entire HTML page as string to be rendered via AJAX
	
    private static function RenderFromPath($path_to_views, $data)
	{
		// Validate view
		
		if ( !view()->exists($path_to_views) )
			return view("render.render_failed", ['error_code' => 0, 'message' => 'View path \'' . $path_to_views . '\' does not exists!']);
		
		// ----------------------------------------------------------------------------------
	
		return view($path_to_views, $data)->render();
	}
	
	// ----------------------------------------------------------------------------------
	
	// Return an entire HTML page as string to be rendered via AJAX
	
    private static function RenderError($data)
	{
		return RenderController::RenderFromPath("render.render_failed", $data);
	}
	
	// ----------------------------------------------------------------------------------
	
    public static function render_gallery(Request $request)
	{
		/*
		Assumed AJAX request structure:
		
		{
			search_query		: (string|optional) // The search criteria from the search bar at the home page
			filter_by_user_id	: (string|optional) // If provided, only shows posts from this user 
		}
		
		*/
		
		// Validate AJAX request structure
		
		// No validation needed since all requests are optional
		
		// ----------------------------------------------------------------------------------

		// Retrieve the posts that fulfill the search query.
		// Make sure the Ajax request is successful.
		
		$latest_posts = AjaxController::ajax_get_latest_post($request);
		$popular_posts = AjaxController::ajax_get_popular_post($request);
		$most_liked_posts = AjaxController::ajax_get_most_liked_post($request);

		$path_to_views = "render.render_gallery";
		$data = [
			'latest_posts'	=> $latest_posts,
			'popular_posts'	=> $popular_posts,
			'most_liked_posts'	=> $most_liked_posts,
		];
		
		return RenderController::RenderFromPath($path_to_views, $data);
	}
	
	// ----------------------------------------------------------------------------------
	
    public static function render_post_comment(Request $request)
	{
		/*
		Assumed AJAX request structure:
		
		{
			'post_id' : (string) // The post id
		}
		
		*/
		
		// Validate AJAX request structure
		
		try {
			$post = Post::findOrFail($request['post_id']);
		} catch (ModelNotFoundException $e) {
			return RenderController::RenderError(['error_code' => 0, 'message' => 'Post no longer exists']);
		}
		
		// ----------------------------------------------------------------------------------

		$comments = $post->comments()
		->where('child_of', null)
		->orderBy('created_at', 'desc')
		->paginate(Common::_int['comment_pp']);
		
		$path_to_views = "render.render_post_comment";
		$data = [
			'comments'	=> $comments,
			'post'		=> $post,	
		];

		return RenderController::RenderFromPath($path_to_views, $data);
	
	}

	// ----------------------------------------------------------------------------------
	
}
