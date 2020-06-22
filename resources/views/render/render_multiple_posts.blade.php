
<?php

// Hide comment from client

// Here displays the HTML of the multiple posts thumbnail

// PARAMETERS:

// $posts	- The posts that need to be displayed

// The following variables are optional
// $show_view_count - Optional. Show view count
// $show_like_count - Optional. Show like count
// $show_sim_index  - Optional. Show similar index

?>
<html>
<head>
    <link href="{{ asset('css/post_index.css') }}" rel="stylesheet">
</head>
<body>
    <div class="scrollbar" id="style-3">
        <div class="force-overflow"></div>
      </div>
    <div>
        @if (count($posts) > 0 )
        <div class="gallery_row">
            @foreach($posts as $i => $post)
            <div class="gallery_row_item" >
                @include('render.render_post_thumbnail', ['post' => $post]) <?php //No need to pass the optional variables ?>
            </div>
            @endforeach
        </div>
        @else
        <div class="gallery_row--empty">
            No records found
        </div>
        @endif 
    </div>
</body>
</html>