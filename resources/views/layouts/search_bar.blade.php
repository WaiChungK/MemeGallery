
<?php

// Hide comment from client

// Here displays the HTML of the search bar

?>

<div class="form col-sm-12">
    <div class="row">
        <input  id="input_search_bar" name="search" type="text" class="form-control col-sm-8" 
                placeholder="Search by hashtags or by title." aria-label="Search" 
                style="margin-bottom:10px;"/>
        <button id="search_button" onclick="search()" class="btn btn-primary col-sm-2"><i class="fa fa-search"></i> Search</button>
    </div>
</div>

<script>
$('document').ready(function(){
    $("#input_search_bar").keypress( e => {
        if(e.keyCode == 13)
            $("#search_button").click();
    });
});

async function search()
{
    var search_keyword = $("#input_search_bar").val();
    await SendRequest(
        "{{ route('render_gallery') }}",
        {
            'search_query' : search_keyword,
        },

        // Callback function when request successed

        function (rendered_html) {
            $("#gallery").empty();
            $("#gallery").html(rendered_html);
        },

        // Callback function when request failed

        function (response) {
            confirm("Something went wrong. " + response);
        }
    );
}
</script>