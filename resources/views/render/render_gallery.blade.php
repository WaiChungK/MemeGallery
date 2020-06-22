
<?php

// Hide comment from client

// Here displays the HTML of the gallery after entered search keyword in the search bar

// PARAMETERS:

// $latest_posts	- The posts that need to be displayed
// $popular_posts	- The posts that need to be displayed
// $most_liked_posts- The posts that need to be displayed

?>

<div id="gallery">
    
    <!-- Latest post -->
    <div class="gallery_row--title"> Latest Post </div>
    @include('render.render_multiple_posts', ['posts' => $latest_posts])

    <hr>
    <!-- Popular post -->
    <div class="gallery_row--title"> Popular Post </div>
    @include('render.render_multiple_posts', ['posts' => $popular_posts, 'show_view_count' => true])

    <hr>
    <!-- Most liked post -->
    <div class="gallery_row--title"> Most Liked Post </div>
    @include('render.render_multiple_posts', ['posts' => $most_liked_posts, 'show_like_count' => true])
    
<div>