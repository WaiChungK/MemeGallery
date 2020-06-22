<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// POST CONTROLLER

Route::get('/', 'PostController@index')
->name('home');

Route::get('/post/add', 'PostController@create')
->name('post.create');

Route::post('/post/add', 'PostController@store')
->name('post.create');

Route::get('/post/{id}', 'PostController@show')
->name('post.show');

Route::get('/post/{id}/edit', 'PostController@edit')
->name('post.edit');

Route::put('/post/{id}', 'PostController@update')
->name('post.update');

Route::delete('/post/{id}', 'PostController@destroy')
->name('post.destroy');


// AJAX CONTROLLER

Route::post('/ajax/get_popular_post', 'AjaxController@ajax_get_popular_post')
->name('ajax_get_popular_post');

Route::post('/ajax/get_latest_post', 'AjaxController@ajax_get_latest_post')
->name('ajax_get_latest_post');

Route::post('/ajax/get_similar_post', 'AjaxController@ajax_get_similar_post')
->name('ajax_get_similar_post');

Route::post('/ajax/get_more_post', 'AjaxController@ajax_get_more_post')
->name('ajax_get_more_post');

Route::post('/ajax/like_post', 'AjaxController@ajax_like_post')
->name('ajax_like_post');

Route::post('/ajax/add_comment', 'AjaxController@ajax_add_comment')
->name('ajax_add_comment');

Route::post('/ajax/reply_comment', 'AjaxController@ajax_reply_comment')
->name('ajax_reply_comment');


// RENDER CONTROLLER

Route::post('/render/post_comment', 'RenderController@render_post_comment')
->name('render_post_comment');

Route::post('/render/gallery', 'RenderController@render_gallery')
->name('render_gallery');

Auth::routes();