
<?php

// Hide comment from client

// Here displays the HTML of the comment's without the replies

// PARAMETERS:

// $comments - The comments that need to be displayed

?>
<link href="{{ asset('css/post_show.css') }}" rel="stylesheet">
<div style="text-align:center">
    {{ $comments->links() }}
</div>

<div id="comment_section">
@if (count($comments) > 0 )
<table class="table table-borderless" style="display:inline-table; margin-top: 15px;">
    <thead>
        <col width='100%'>
    </thead>
    <tbody>
        @foreach($comments as $comment)
        <?php $replies = $comment->replies()->orderBy('created_at', 'asc')->get(); ?>
        <tr id="{{$comment->id}}">
            <td style="padding:0 0 0 15px;">
                <div style="margin-top: 10px; height:auto; font-size: 14px; margin-bottom: 3px;" class="comment_box">{{$comment->message}}</div>

                <span style="font-size:10px; padding-left: 5px; color: grey;" class="glyphicon glyphicon-pencil"></span>
                <span style="font-size: 13px; color: grey;">{{$comment->user->name}}</span>
                <span style="font-size:10px; padding-left: 24px; color: grey;" class="glyphicon glyphicon-time"> </span>
                <span style="font-size: 12px; color: grey;"> {{date('d-M-Y h:i a', strtotime($comment->created_at))}}</span>
                @guest
                @else
                <button class="reply-button btn reply_button" >Reply</button>
                <div class="reply-form"></div>
                @endguest
                <div style='margin:5px 0px;'>
                    @if (count($replies) > 0)
                    <span class="see-replies" role="button" data-toggle="collapse" data-target="#replies_{{$comment->id}}">
                        <i class="glyphicon glyphicon-triangle-bottom" style="padding-right:2px;"></i> See replies ( {{count($replies)}} replies )
                    </span>
                    @endif
                </div>
            </td>
        </tr>
        @if (count($replies) > 0)
        <tr>
            <td>@include('render.render_post_comment_reply', ['replies'=>$replies, 'parent_id' => $comment->id])</td>
        </tr>
        @endif

        @endforeach
    </tbody>
</table>
@else
<div class="first_comment"> Be the first to comment!</div>
@endif
</div>

<div style="text-align:center">
    {{ $comments->links() }}
</div>

<script>


// When click on the REPLY button on the bottom of the comment message textarea,
// Display the reply form,
// hide the reply button

$(".reply-button").on('click', function() {

    $(this).parent().find('.reply-button').hide();
    $(this).parent().find('.reply-form').show();
    appendReplyCommentForm( $(this).parent().find('.reply-form').get(0) );
    
    // After that,

    // Apply the following events on the CANCEL button in the form
    // When click, hide the reply form,
    // show the reply button  

    $(this).parent().find(".input-cancel-button").on('click', function() {
        $(this).parent().parent().parent().parent().find('.reply-button').show();
        $(this).parent().parent().parent().hide();
    })

    // Apply the following event
    // When user enter a new line character,
    // increase the number of rows of the textarea
    
    $(".input-comment-reply").on('input', function(e) {
        var newline = $(this).val().split(/[\n\r]/g).length;
        $(this).prop('rows', newline + ($(this).val().endsWith('\n')?-1:0) );
    })
})

// Make all textareas show 100% of the content without need to scroll

$(".show-all-text").each(function(i, obj){
    var newline = $(obj).val().split(/[\n\r]/g).length;
    $(obj).prop('rows', newline + ($(obj).val().endsWith('\n')?-1:0) );
})

// Append the reply form to the specified html

function appendReplyCommentForm(html)
{
    // If the user clicked the CANCEL button, keeps all the text.
    // when the user clicked REPLY button, show the reply form with the message entered previously.
    // This is achieved by checking if the innerHTML is emptied
    // if empty means the user haven't clicked on the REPLY button and vice versa

     // Check if the user hadn't clicked the CANCEL button
    if ( html.innerHTML === '')
    {
        html.innerHTML = "\
            <div id='comment_reply_form' class='col-sm-12'>\
                <div class='col-sm-12'>\
                    <textarea class='input-comment-reply form-control' placeholder='Put your comment here.' rows=1 cols=150></textarea>\
                </div>\
                <div class='col-md-3 col-md-offset-9' style='padding:10px;0px;'>\
                    <button class='input-cancel-button btn btn-danger'>Cancel</button>\
                    <button class='input-reply-button btn btn-primary'>Reply</button>\
                </div>\
            </div>\
            ";
        
        // Apply the following event when user clicked the REPLY button
        // Send a request to the server to add the comment,
        // Then refresh the comment section

        $(".input-reply-button").on('click', async function()
        {
            let textarea = $(this).parent().parent().find('.input-comment-reply');

            if ( textarea.val().trim() === '' ) return;
            
            // Add comment

            let status = await SendRequest(
                "{{route('ajax_add_comment')}}",
                {
                    post_id	: {{$post->id}}, // The post id 
                    user_id	: 0, // The user id 
                    message : textarea.val().trim(),
                    comment_id : $(this).parent().parent().parent().parent().parent().prop('id'),
                },
                function(status) { 
                    textarea.val('');
                    return JSON.parse(status)['success']; 
                },
                function(error) { alert('Something went wrong.\nError message: ' + error); }
            );

            // If the comment had been created successfully,
            // refresnhtne comment section 

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
                alert('Failed to reply. Please try again.');
            }
        });
    }
}
</script>