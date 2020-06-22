<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\User;
use App\Post;
use App\PostComment;
use App\PostLike;

use App\Utility\Utility as Util;

/*
Tips:

->skip(1)->first() // will return the second record
->skip(1)->take(2) // 
*/

class AjaxController extends Controller
{
    
	// ----------------------------------------------------------------------------------
	
	// Request failed template

	private static function RequestFailed($err_code, $err_msg)
	{
		return response()->json([
			'error_code'=> $err_code,
			'message'	=> $err_msg,
		], 401);
	}
	
	// ----------------------------------------------------------------------------------
	
	// Usage: Filter posts under many credentials
	
	private static function helper_filter_post($request)
	{
		/*
		Assumed AJAX request structure:
		
		{
			filter_by_user_id|OR	: (string|optional)
			filter_by_user_name|OR	: (string|optional)
			filter_by_title|OR		: (string|optional)
			filter_by_message|OR	: (string|optional)
			filter_by_hashtags|OR	: (string|optional)
			// filter_by_media		: (string|optional)
			filter_by_view_gt	: (int|optional) // Greater than
			filter_by_view_lt	: (int|optional) // Less than
			filter_by_view_eq	: (int|optional) // Equal
			// If gt & eq exists at the same time, it becomes greater than or equal
			// If lt & eq exists at the same time, it becomes lesser than or equal
			// If gt & lt exists at the same time, invalid request
			
			filter_by_like		: (string|optional) // implementation not needed
			filter_by_dislike	: (string|optional) // implementation not needed
			filter_by_comment	: (string|optional) // implementation not needed
			
			return_size		: (integer|optional) // Return the first N items. If not specified, return all items instead
			return_as_array	: (boolean|optional) // Default is false. If true, return the result as array
			
			// if in the future we discover more feature to be filtered, add it here
			
		}
		
		*/
		
		// Validation 
		
		// ----------------------------------------------------------------------------------
	
		$results = Post::join('users', 'posts.user_id', '=', 'users.id')
		->select("users.*")
		->select("posts.*");
		
		// Processing basic information

		if ( isset($request['filter_by_user_id']) ) $results = $results->where('users.id', '=', $request['filter_by_user_id']);
		if ( isset($request['filter_by_user_name']) ) $results = $results->where('users.name', 'like', '%'.$request['filter_by_user_name'].'%');
		if ( isset($request['filter_by_title']) ) $results = $results->where('posts.title', 'like', '%'.$request['filter_by_title'].'%');
		if ( isset($request['filter_by_message']) ) foreach (explode(' ', $request['filter_by_message']) as $msg) $results = $results->where('posts.description', 'like', '%'.$msg.'%');
		if ( isset($request['filter_by_hashtags']) ) foreach (explode(' ', $request['filter_by_hashtags']) as $msg) $results = $results->where('posts.description', 'like', '%#'.$msg.'%');
		
		// Processing view count

		if ( isset($request['filter_by_view_gt']) ) $results = $results->where('posts.view', '>=', $request['filter_by_view_eq']);
		else if ( isset( $request['filter_by_view_lt']) ) $results = $results->where('posts.view', '<=', $request['filter_by_view_eq']);
		else if ( isset($request['filter_by_view_gt']) ) return AjaxController::RequestFailed(0, 'Bad request. Cannot filter with greater than and less than at the same time.');
		else if ( isset($request['filter_by_view_gt']) ) $results = $results->where('posts.view', '>', $request['filter_by_view_gt']);
		else if ( isset($request['filter_by_view_lt']) ) $results = $results->where('posts.view', '<', $request['filter_by_view_lt']);
		else if ( isset($request['filter_by_view_eq']) ) $results = $results->where('posts.view', '=', $request['filter_by_view_eq']);
		
		// NOTE:
		// OR conditions must be placed after AND conditions to avoid SQL syntax error

		// Processnig basic information

		if ( isset($request['filter_by_user_id_OR']) ) $results = $results->orWhere('users.id', '=', $request['filter_by_user_id_OR']);
		if ( isset($request['filter_by_user_name_OR']) ) $results = $results->orWhere('users.name', 'like', '%'.$request['filter_by_user_name_OR'].'%');
		if ( isset($request['filter_by_title_OR']) ) $results = $results->orWhere('posts.title', 'like', '%'.$request['filter_by_title_OR'].'%');
		if ( isset($request['filter_by_message_OR']) ) foreach (explode(' ', $request['filter_by_message_OR']) as $msg) $results = $results->orWhere('posts.description', 'like', '%'.$msg.'%');
		if ( isset($request['filter_by_hashtags_OR']) ) foreach (explode(' ', $request['filter_by_hashtags_OR']) as $msg) $results = $results->orWhere('posts.description', 'like', '%#'.$msg.'%');

		// Processing other information
		
		if ( isset($request['return_size']) && $request['return_size'] >= 0 ) $results = $results->limit($request['return_size']);
		if ( isset($request['return_as_array']) && $request['return_as_array'] ) $results = $results->get();
		
		return $results;
	}
	
	// ----------------------------------------------------------------------------------
	
	public static function ajax_get_latest_post($request)
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
		
		// Processing criteria

		if ( isset($request['search_query']) )
		{
			$request['filter_by_title_OR'] = $request['search_query'];
			$request['filter_by_hashtags'] = $request['search_query'];
		}

		// No need to process 'filter_by_user_id' as it already has the definition
		
		$request['return_size'] = 20;
		
		// ----------------------------------------------------------------------------------
		
		$posts = AjaxController::helper_filter_post($request)
		->orderBy('created_at', 'desc')
		->get();

		return $posts;
	}
	
	// ----------------------------------------------------------------------------------
	
	public static function ajax_get_popular_post($request)
	{
		/*
		Assumed AJAX request structure:
		
		{
			search_query	: (string|optional) // The search criteria from the search bar at the home page
			filter_by_user_id	: (string|optional) // If provided, only shows posts from this user 
		}
		
		*/
		
		// Validate AJAX request structure
		
		// No validation needed since all requests are optional
		
		// ----------------------------------------------------------------------------------
		
		// Processing criteria

		if ( isset($request['search_query']) )
		{
			$request['filter_by_title_OR'] = $request['search_query'];
			$request['filter_by_hashtags_OR'] = $request['search_query'];
		}

		// No need to process 'filter_by_user_id' as it already has the definition
		
		$request['return_size'] = 20;
		
		// ----------------------------------------------------------------------------------
		
		$posts = AjaxController::helper_filter_post($request)
		->orderBy('view', 'desc')
		->get();

		return $posts;
	}

	// ----------------------------------------------------------------------------------
	
	public static function ajax_get_most_liked_post($request)
	{
		/*
		Assumed AJAX request structure:
		
		{
			search_query	: (string|optional) // The search criteria from the search bar at the home page
			filter_by_user_id	: (string|optional) // If provided, only shows posts from this user 
		}
		
		*/
		
		// Validate AJAX request structure
		
		// No validation needed since all requests are optional
		
		// ----------------------------------------------------------------------------------
		
		// Processing criteria

		if ( isset($request['search_query']) )
		{
			$request['filter_by_title_OR'] = $request['search_query'];
			$request['filter_by_hashtags_OR'] = $request['search_query'];
		}

		// No need to process 'filter_by_user_id' as it already has the definition
		
		$request['return_as_array'] = true;
		
		// ----------------------------------------------------------------------------------
		
		$posts = AjaxController::helper_filter_post($request)
		->each(function(Post $post){ $post->likeCount = $post->likeCount(); })
		->sortByDesc('likeCount')
		->take(20);

		return $posts;
	}
	
	
	// ----------------------------------------------------------------------------------
	
	public static function ajax_get_similar_post($request)
	{
		/*
		Assumed AJAX request structure:
		
		{
			post_id	: (string) // The post id
		}
		
		*/
		
		// Validate AJAX request structure
		
		// Check if the post_id is legal

		if ( !isset($request['post_id']) ) 
			return AjaxController::RequestFailed(0, '\'post_id\' credential is missing from the request.');
		
		try
		{
			$this_post = Post::findOrFail( $request['post_id'] );
		}
		catch( ModelNotFoundException $e )
		{
			return AjaxController::RequestFailed(0, 'Post with post_id == \''.$request["post_id"].'\' does not exists.');
		}

		// ----------------------------------------------------------------------------------
		
		$ht1 = Util::FindHashtags($this_post->description);
		
		$posts = Post::where('id', '<>', $request['post_id'])
		->get()
		->each( function($post) use($ht1) {
			$ht2 = Util::FindHashtags($post->description);
			$post->similarity = Util::HashtagsDistance($ht1, $ht2);
		} )
		->filter( function($post){ return $post->similarity > 0.5; } )
		->sortByDesc('similarity')
		->take(20);
		return $posts;
	}
	
	// ----------------------------------------------------------------------------------
	
	// Get more posts that were posted by this user
	
	public static function ajax_get_more_post($request)
	{
		/*
		Assumed AJAX request structure:
		
		{
			post_id	: (string) // The user id
		}
		
		*/
		
		// Validate AJAX request structure
		
		// Check if the post_id is legal

		if ( !isset($request['post_id']) ) 
			return AjaxController::RequestFailed(0, '\'post_id\' credential is missing from the request.');
		
		try
		{
			$post = Post::findOrFail( $request['post_id'] );
			$user_id = $post->user_id;
		}
		catch( ModelNotFoundException $e )
		{
			return AjaxController::RequestFailed(0, 'Post with post_id == \''.$request["post_id"].'\' does not exists.');
		}

		// ----------------------------------------------------------------------------------
		
		// Processing criteria
		
		$request['filter_by_user_id'] = $user_id;
		$request['return_size'] = 20;
		
		// ----------------------------------------------------------------------------------
		
		$posts = AjaxController::helper_filter_post($request)
		->get()
		->shuffle();
		return $posts;
	}
	
	// ----------------------------------------------------------------------------------
	
	public static function ajax_like_post(Request $request)
	{
		/*
		Assumed AJAX request structure:
		
		{
			post_id	: (string) // The post id
			user_id	: (string) // The user id 
			status	: (string) // Like or dislike
		}
		
		*/
		
		// Validate AJAX request structure
		
		// return AjaxController::RequestFailed(0, '');
		
		// ----------------------------------------------------------------------------------
		
		// Like/Dislike post
		
		$status = [ 'success' => true ];
		
		try
		{
			$post = PostLike::updateOrCreate(
				[
					'user_id'	=> $request['user_id'],
					'post_id'	=> $request['post_id'],
				],
				[
					'status'	=> $request['status'],
				]
			)->post;
			$status['like']	= $post->likeCount();
			$status['dislike'] = $post->dislikeCount();
		} 
		catch(QueryException $e)
		{
			$status['success'] = false;
			$status['message'] = $e->getMessage();
		}
		catch (ModelNotFoundException $e)
		{
			$status['success'] = false;
			$status['message'] = $e->getMessage();
		}

		return json_encode($status);
		
	}
	
	// ----------------------------------------------------------------------------------
	
	public static function ajax_add_comment(Request $request)
	{
		/*
		Assumed AJAX request structure:
		
		{
			post_id	: (string) // The post id 
			user_id	: (string) // The user id 
			message	: (string) // The message of the comment
			comment_id	: (optional|string) // The comment id 
		}
		
		*/
		
		// Validate AJAX request structure
		
		// return AjaxController::RequestFailed(0, '');
		
		// ----------------------------------------------------------------------------------
		
		// Add comment
		
		$status = [ 'success' => true ];

		try
		{
			PostComment::create([
				'user_id'	=> Auth::user()->id,
				'post_id'	=> $request['post_id'],
				'message'	=> $request['message'],
				'child_of'	=> isset($request['comment_id']) ? $request['comment_id'] : null,
			]);
		} 
		catch(QueryException $e)
		{
			$status['success'] = false;
		}
		catch (Exception $e)
		{
			$status['success'] = false;
		}
		
		return json_encode($status);
		
	}
	
	// ----------------------------------------------------------------------------------
	
	
}
