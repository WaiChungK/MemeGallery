
<?php

// Hide comment from client

// Here displays the HTML of the post thumbnail

// PARAMETERS:

// $post	- The post that need to be displayed

// The following variables are optional
// $show_view_count - Optional. Show view count
// $show_like_count - Optional. Show like count
// $show_sim_index  - Optional. Show similar index

?>
<link href="{{ asset('css/post_index.css') }}" rel="stylesheet">
<a href="{{route('post.show', ['id'=>$post->id])}}" class="thumbnail">
    <div class="thumbnail_image--title">{{$post->title}}</div>
   
    @if ( isset($show_view_count) && $show_view_count )
    <div class="thumbnail_image--attribute">{{$post->view}} views</div>
    @endif

    @if ( isset($show_like_count) && $show_like_count )
    <div class="thumbnail_image--attribute">{{$post->likeCount()}} likes</div>
    @endif

    @if ( isset($show_sim_index) && $show_sim_index )
    <div class="thumbnail_image--attribute">{{number_format($post->similarity*100, 2)}}%</div>
    @endif

    @if ($post->media === 'video')
    <video width="200" height="200" autoplay loop muted>
        <source src="{{$post->mediaSource()}}" type="video/mp4">
        <source src="{{$post->mediaSource()}}" type="video/ogg">
        <source src="{{$post->mediaSource()}}" type="video/webm">
        <source src="{{$post->mediaSource()}}" type="video/wmv">
        Your browser does not support the video tag.
    </video>
    @elseif ($post->media === 'image' || $post->media === 'gif')
    <img src="{{$post->mediaSource()}}" style="width:200px;height:200px;margin-top:10px;" />
    @else
    <span>Error. Unknown media type.</span>
    @endif


</a>