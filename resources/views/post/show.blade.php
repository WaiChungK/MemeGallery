
<?php

// Hide comment from client

// Here displays the information of an individual post.

// PARAMETERS:

// $post		- The post that need to be displayed
// $sim_posts	- The posts that were similar to the current post
// $more_posts	- The posts that were posted by the same user
// $comments	- The comment of the post

use App\Common;
use Illuminate\Support\Facades\Gate;

?>
<link href="{{ asset('css/post_show.css') }}" rel="stylesheet">
@extends('app')

@section('content')
<div class="container">

    @include('layouts.error')

    <div class="col-sm-12">

        <!-- TITLE -->

        <div class="col-sm-12" style="text-align:left;">
            {{-- <h3 class="control-label">{{$post->title}}</h3> --}}
            <div class="page-header page_show_title">
                <h3 style="font-weight:bold;font-size:20px;line-height:1.5;">{{$post->title}}</h3>
            </div>
        </div>
        
        <!-- MEDIA -->
        <div class='col-sm-7' style="padding-left:0; padding-right:0">
            <div class="col-sm-12" style="padding-left:0px; padding-right:0">
            @if ($post->media === 'video')
                <video class="col-sm-12" width="300" controls>
                    <source src="{{$post->mediaSource()}}" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
                @elseif ($post->media === 'image' || $post->media === 'gif')
                <img class="col-sm-12" src="{{$post->mediaSource()}}" width=300 />
                @else
                <span>Error. Unknown media type.</span>
            @endif
            </div>
        </div>

        <div class="col-sm-5" style="text-align:left; margin-bottom: 30px;">
        <div class="post_show_description">
            <table class='col-sm-12 table table-borderless' style="font-size:14px;">
                <col width=50%>
                <col width=50%>
                <!-- USER -->
                <tr>
                    @if(Gate::allows('update-post', $post))
                    <td></td>
                    <td style="display:flex;flex-direction:row; justify-content:flex-end;padding:0;height:20px;">
                    <a href="{{ url('/post/' . $post->id . '/edit') }}" class="post_show_edit" title="Edit Meme" >
                            <svg class="bi bi-pencil-square" width="20px" height="20px" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path d="M15.502 1.94a.5.5 0 010 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 01.707 0l1.293 1.293zm-1.75 2.456l-2-2L4.939 9.21a.5.5 0 00-.121.196l-.805 2.414a.25.25 0 00.316.316l2.414-.805a.5.5 0 00.196-.12l6.813-6.814z"/>
                            <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 002.5 15h11a1.5 1.5 0 001.5-1.5v-6a.5.5 0 00-1 0v6a.5.5 0 01-.5.5h-11a.5.5 0 01-.5-.5v-11a.5.5 0 01.5-.5H9a.5.5 0 000-1H2.5A1.5 1.5 0 001 2.5v11z" clip-rule="evenodd"/>
                            </svg>
                    </a>
                    <form action="{{ route('post.destroy', $post->id) }}" method="POST">
                        {{ method_field('DELETE') }}
                        {{ csrf_field() }}
                        <button class="post_show_delete" title="Delete Meme" > 
                            <svg class="bi bi-trash" width="20px" height="20px" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5.5 5.5A.5.5 0 016 6v6a.5.5 0 01-1 0V6a.5.5 0 01.5-.5zm2.5 0a.5.5 0 01.5.5v6a.5.5 0 01-1 0V6a.5.5 0 01.5-.5zm3 .5a.5.5 0 00-1 0v6a.5.5 0 001 0V6z"/>
                            <path fill-rule="evenodd" d="M14.5 3a1 1 0 01-1 1H13v9a2 2 0 01-2 2H5a2 2 0 01-2-2V4h-.5a1 1 0 01-1-1V2a1 1 0 011-1H6a1 1 0 011-1h2a1 1 0 011 1h3.5a1 1 0 011 1v1zM4.118 4L4 4.059V13a1 1 0 001 1h6a1 1 0 001-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </form>
                    </td>
                    @endif
                </tr>
                <tr>
                    <td><label class="control-label">Uploaded by</label></td>
                    <td><label class="control-label">{{$post->user->name}}</label></td>
                </tr>

                <!-- VIEW -->
                <tr>
                    <td><label class="control-label">View count</label></td>
                    <td><label class="control-label">{{$post->view}}</label></td>
                </tr>

                <!-- MEDIA TYPE -->
                <tr>
                    <td><label class="control-label">Media Type</label></td>
                    <td><label class="control-label">{{Common::_desc['media'][$post->media]}}</label></td>
                </tr>
                
                <!-- DESCRIPTION -->
                <tr>
                    <td><label class="control-label">Description</label></td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan=2>
                        {{ Form::textarea('description', $post->description, [
                            'id'    => 'description',
                            'class' => 'form-control show-all-text',
                            'readonly',
                            'style' => 'height:100px',
                        ]) }}
                    </td>
                </tr>

                <!-- LIKE AND DISLIKE -->
                @guest
                <tr>
                    <td>
                        <br>
                        <button id="like_button" title="Like this post" class="btn btn-success btn-sm" disabled>
                            <span class="glyphicon glyphicon-thumbs-up"></span> Like
                        </button>
                        <button id="dislike_button" title="Dislike this post" class="btn btn-danger btn-sm" disabled>
                            <span class="glyphicon glyphicon-thumbs-down"></span> Dislike
                        </button>
                    </td>
                    <td>
                        <br>
                        <span id="lk_bar" style="font-size:15px;line-height:2;" title="{{number_format($post->likeCount())}} likes {{number_format($post->dislikeCount())}} dislikes">{{number_format($post->likeCount())}}/{{number_format($post->dislikeCount())}} </span>
                    </td>
                </tr>
                @else
                <tr>
                    <td>
                        <br>
                        <button id="like_button" title="Like this post" class="btn btn-success btn-sm">
                            <span class="glyphicon glyphicon-thumbs-up"></span> Like
                        </button>
                        <button id="dislike_button" title="Dislike this post" class="btn btn-danger btn-sm">
                            <span class="glyphicon glyphicon-thumbs-down"></span> Dislike
                        </button>
                    </td>
                    <td>
                        <br>
                        <span id="lk_bar" style="font-size:15px;line-height:2;" title="{{number_format($post->likeCount())}} likes {{number_format($post->dislikeCount())}} dislikes">{{number_format($post->likeCount())}}/{{number_format($post->dislikeCount())}} </span>
                    </td>
                </tr>
                @endguest
            </table>
        
        
        </div>
        </div>
        <hr>
        <!-- Similar posts -->

        <span class="col-sm-12 post_show_similar">Similar posts</span>
        <div class="post_show_other">
        @include('render.render_multiple_posts', ['posts'=>$sim_posts, 'show_sim_index' => true])
        </div>

        <!-- More posts from same user -->
        <span class="col-sm-12 post_show_similar">More posts from this user</span>
        <div class="post_show_other">
        @include('render.render_multiple_posts', ['posts'=>$more_posts, 'show_view_count' => true])
        </div>
        <!-- Comment section -->
        
        <!-- Comment Form -->
        @guest
        <div id="comment_form" class="col-sm-12" style="margin-top:15px;">
            <div class="col-sm-8" style="padding-left:0">
                {{Form::textarea('', null, [
                    'id'    => 'input_comment_message',
                    'class' => 'form-control',
                    'placeholder'   => 'To comment, please login to an account.',
                    'rows'  => 4,
                    'cols'  => 50,
                    'style' => "height:50px;",
                    'readonly' => 'true'
                ])}}
            </div>
        </div>
        @else
        <div id="comment_form" class="col-sm-12" style="margin-top:15px;">
            <div class="col-sm-8" style="padding-left:0">
                {{Form::textarea('comment_message', null, [
                    'id'    => 'input_comment_message',
                    'class' => 'form-control',
                    'placeholder'   => 'Put your comment here.',
                    'rows'  => 4,
                    'cols'  => 50,
                    'style' => "height:50px;"
                ])}}
            </div>
            <button id="input_comment_button" class="btn btn-primary" style="position:absolute; bottom:0%">Comment</button>
        </div>
        @endguest
        <!-- Comments -->

        @include('render.render_post_comment', ['comments' => $comments])

    </div>
</div>

<script>
async function LikeDislikePost(status)
{
    await SendRequest(
        "{{route('ajax_like_post')}}",
        {
            'user_id'   : @if(Auth::check()) {{Auth::user()->id}} @else 0 @endif,
            'post_id'   : {{$post->id}},
            'status'    : status,
        },

        // Succeed

        function(response) {
            response = JSON.parse(response);
            if (response['success'])
            {
                const like = response['like'];
                const dislike = response['dislike'];
                $("#lk_bar").html(like + '/' + dislike);
                $("#lk_bar").get(0).title = like + " likes " + dislike + " dislikes";
            }
        },

        // Failed 

        function (error) { alert('Something went wrong.\nError message: ' + error); }
    );
}

$("#like_button").on('click', async function(){
    await LikeDislikePost("{{Common::_char['like']}}");
});
$("#dislike_button").on('click', async function(){
    await LikeDislikePost("{{Common::_char['dislike']}}");
});

// Send a request to the server to add the comment,
// Then refresh the comment section

$("#input_comment_button").get(0).onclick = async function()
{
    if ( $("#input_comment_message").val().trim() === '' ) return;
    
    // Add comment

    let status = await SendRequest(
        "{{route('ajax_add_comment')}}",
        {
			post_id	: {{$post->id}}, // The post id 
			user_id	: @if(Auth::check()) {{Auth::user()->id}} @else 0 @endif,// The user id 
            message : $("#input_comment_message").val().trim(),
        },
        
        // Succeed

        function (status)
        {
            $("#input_comment_message").val('');
            return JSON.parse(status)['success'];
        },

        // Failed 

        function (error) { alert('Something went wrong.\nError message: ' + error); }

    );
    
    if ( status )
    {
        await SendRequest(
            "{{route('render_post_comment')}}",
            {
                'post_id'   : {{$post->id}},
            },
            function (rendered_html) 
            {
                $("#comment_section").empty();
                $("#comment_section").html(rendered_html);
            },
            function (error)
            {
                alert('Something went wrong.\nError message: ' + error);
            }
        )
    }
    else
    {
        alert('Failed to comment. Please try again.');
    }

}

</script>
@endsection
