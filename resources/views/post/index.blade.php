
<?php

// Hide comment from client

// This is the home page. Here displays the gallery of the posts.

// PARAMETERS:

// $latest_posts	- The posts that need to be displayed
// $popular_posts	- The posts that need to be displayed
// $most_liked_posts- The posts that need to be displayed

use App\Common;

?>

@extends('app')
@section('content')
<link href="{{ asset('css/post_index.css') }}" rel="stylesheet">
<div class='container'>

    <!-- Search form -->
    @include('layouts.search_bar')

    <!-- Gallery -->
    @include('render.render_gallery', [
        'latest_post'   => $latest_posts,
        'popular_post'  => $popular_posts,
        'most_liked_posts'  => $most_liked_posts,
    ])
    
</div>
@endsection
