
<?php

// Hide comment from client

// Here displays the HTML of the comment's replies

// PARAMETERS:

// $replies     - The replies of a comment that need to be displayed
// $parent_id   - The id of the parent comment

?>
<link href="{{ asset('css/post_show.css') }}" rel="stylesheet">
<div id="replies_{{$parent_id}}" class="col-md-12 collapse">
<table class="table table-borderless" style="background-color: transparent; padding-left: 25px; display:inline-block; margin-bottom: 0">
    <thead>
    </thead>
    <tbody>
        @foreach($replies as $comment)
        <tr>
            <td>
                <span style="font-size:8px; padding-left: 5px; color: grey;" class="glyphicon glyphicon-pencil"></span>
                <span style="font-size: 10px; color: grey;">{{$comment->user->name}}</span>
                <span style="font-size: 8px; padding-left: 24px; color: grey;" class="glyphicon glyphicon-time"> </span>
                <span style="font-size: 10px; color: grey;"> {{date('d-M-Y h:i a', strtotime($comment->created_at))}}</span>
                <div style="margin-top: 2px; height:auto; font-size: 12px; margin-bottom: 3px;" class="comment_box">{{$comment->message}}</div>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
</div>