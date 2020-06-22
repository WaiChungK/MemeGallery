<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

use App\Post;
use App\PostComment;
use App\PostLike;

use App\Common;
use App\Utility\Utility as Util;
use App\Http\Controllers\AjaxController;

class PostController extends Controller
{
	// ----------------------------------------------------------------------------------
	
	// This is the home page, where you display the gallery
	
    public function index()
	{
		$latest_posts = AjaxController::ajax_get_latest_post([]);
		$popular_posts = AjaxController::ajax_get_popular_post([]);
		$most_liked_posts = AjaxController::ajax_get_most_liked_post([]);
		
		return view('post.index',[
			'latest_posts' => $latest_posts,
			'popular_posts' => $popular_posts,
			'most_liked_posts' => $most_liked_posts,
		]);
	}
	
	// ----------------------------------------------------------------------------------
	
	public function show($id)
	{
		// Show an individual post 
		
		// $post		- The post that need to be displayed
		// $sim_posts	- The posts that were similar to the current post
		// $more_posts	- The posts that were posted by the same user
		// $comments	- The comment of the post

		try {
			$post = Post::findOrFail($id);
			// Increment the view
			$post->update([
				'view'	=> $post->view+1 
			]);
			
			$comments = $post->comments()
			->where('child_of', null)
			->orderBy('created_at', 'desc')
			->paginate(Common::_int['comment_pp']);

			$request = [ 
				'post_id' => $post->id ,
			];

			$sim_posts = AjaxController::ajax_get_similar_post($request);
			$more_posts = AjaxController::ajax_get_more_post($request);
			
		} catch( ModelNotFoundException $e) {
			return back()
			->with('errors', 'Post does not exists.');
		} catch (Exception $e) {
			return back()
			->with('errors', $e->getMessage());
		}
		
		return view('post.show', [
			'post'		=> $post,
			'comments'	=> $comments,
			'sim_posts'	=> $sim_posts,
			'more_posts'=> $more_posts,
		]);
	}
	
	// ----------------------------------------------------------------------------------
	
	public function create()
	{
		// Show the form to create a post
		
		return view('post.create');
	}
	
	// ----------------------------------------------------------------------------------
	
	public function store(Request $request)
	{
		// Validation
		
		$val_file = "required";
		switch( $request['media'] )
		{
			case "image": $val_file .= "|max:2097152|dimensions:min_width=100,min_height=100|mimes:jpeg,jpg,png"; break;
			case "gif": $val_file .= "|max:2097152|mimes:gif"; break;
			case "video": $val_file .= "|max:2097152|mimes:ogg,mp4,webm,wmv"; break;
		}
		
		$request->validate([
			'title'	=> "required|max:".Common::_len['post']['title'],
			'description'	=> "max:".Common::_len['post']['description'],
			'media'	=> "required",
			'file'	=>	$val_file,
		]);
		
		// =============== END OF VALIDATION ===============
	
		// Add post
		
		$post = Post::create([
			'user_id'	=> Auth::user()->id,	// Get the current user's id
			'title'	=> $request['title'],
			'description'	=>	$request['description'],
			'view'	=> 0,
			'media'	=> $request['media'],
			'image_path'	=> '', // update it after obtain the image path
		]);

		// Add picture 

		$destination = public_path(Common::image_post_path);
		$ext = strtolower($request['file']->getClientOriginalExtension()); // Get the extension without the '.'
		$file_name =  $post->id . '.' . $ext;
		$request['file']->move($destination, $file_name);
		
		// Update image_path column

		$post->update([
			'image_path'	=> $file_name,
		]);
	
		return redirect()
		->route('home')
		->with('success', 'Post created successfully.');
	}
	
	// ----------------------------------------------------------------------------------
	
	public function edit($id)
	{
		// $post		- The post that need to be displayed

		try{
			$post = Post::findOrFail($id);
		} catch( ModelNotFoundException $e) {
			return back()
			->with('errors', 'Post does not exists.');
		} catch (Exception $e) {
			return back()
			->with('errors', $e->getMessage());
		}

		if (Gate::allows('update-post', $post)) {
			return view('post.edit', [
				'post' => $post,
			]);
		} else {
			return back()->withInput()->withErrors(['Unauthorized user.']);
		}

	}
	
	// ----------------------------------------------------------------------------------
	
	public function update(Request $request, $id)
	{
		// Validation
		
		$val_file = "";
		switch( $request['media'] )
		{
			case "image": $val_file .= "|max:2097152|dimensions:min_width=100,min_height=100|mimes:jpeg,jpg,png"; break;
			case "gif": $val_file .= "|max:2097152|mimes:gif"; break;
			case "video": $val_file .= "|max:2097152|mimes:ogg,mp4,webm,wmv"; break;
		}
		
		$request->validate([
			'title'	=> "required|max:".Common::_len['post']['title'],
			'description'	=> "max:".Common::_len['post']['description'],
			'media'	=> "required",
			'file'	=>	$val_file,
		]);
		
		// =============== END OF VALIDATION ===============
	
		// Update post

		try{
			$post = Post::findOrFail($id);

			$post->title = $request->title;
			$post->description = $request->description;
			$post->media= $request->media;

			// Add picture 
			// https://laravel.com/docs/5.5/requests#retrieving-uploaded-files
			// RETRIEVED 27 March 2020
			if ($request->hasFile('file')){
				$destination = public_path(Common::image_post_path);
				$ext = strtolower($request['file']->getClientOriginalExtension()); // Get the extension without the '.'
				$file_name =  $post->id . '.' . $ext;
				$request['file']->move($destination, $file_name);
				
				// Update image_path column

				$post->update([
					'image_path' => $file_name,
				]);
			}
			$post->save();
		} catch( ModelNotFoundException $e) {
			return  back()->withInput()->withErrors(['Error Post does not exist']);
		} catch (Exception $e) {
			return  back()->withInput()->withErrors([$e->getMessage()]);
		}
		
		return redirect()->route('post.show', ['id' => $post->id])
		->with('success', 'Post updated successfully.');
		// return redirect()
		// ->route('home')
		// ->with('success', 'Post updated successfully.');
	}
	
	// ----------------------------------------------------------------------------------
	
	public function destroy($id)
	{
		// Delete post
		// Delete anythings that are related to post
		// Delete post comment
		// Delete post like

		try {
			$post = Post::findOrFail($id);
		} catch( ModelNotFoundException $e) {
			return back()
			->with('errors', 'Post does not exists.');
		} catch (Exception $e) {
			return back()
			->with('errors', $e->getMessage());
		}
		
		if (Gate::allows('update-post', $post)) {
			//Getting file path + file name
			$image_file = $post->image_path;
			$destination = public_path(Common::image_post_path);
			$image_path = $destination.$image_file;

			//deleting Post with the relations
			Post::where('id', '=', $id)->first()->delete();

			//Deleting the file stored in public/ folder
			if (File::exists($image_path)){
				File::delete($image_path);
			}
			return redirect()->route('home')
							->with('success','Post deleted successfully');
		} else {
			return back()->withInput()->withErrors(['Unauthorized user. Unable to delete.']);
		}
	}
	
	// ----------------------------------------------------------------------------------
}
