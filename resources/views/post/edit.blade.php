
<?php

// Hide comment from client

// Here displays the form to crate a post.

// PARAMETERS:

// $posts	- The post to be edited

use App\Common;

$title_max_len  = Common::_len["post"]["title"];
$desc_max_len   = Common::_len["post"]["description"];
$default_media  = array_keys(Common::_desc['media'])[0];
?>
<link href="{{ asset('css/post_input.css') }}" rel="stylesheet">
@extends('app')

@section('content')
<div class="container">

    @include('layouts.error')
    
    <div class="row post_input_body">
    <div class="post_input_card">
        {!! Form::model($post, [
            'route'     => ['post.update', $post->id],
            'method'    => 'put',
            'id'        => 'form_edit_post',
			'class'		=> 'form-horizontal',
            'files'     => true,
        ])!!}
            <h3 class="post_input_card--title">{{$post->title}} <span class="label label-info"> Edit </span></h3>
            <hr>
            <!-- TITLE -->

            <div class="form-group">
                <label for="title" class="col-md-4 control-label">Title</label>

                <div class="col-md-6">
                    {{ Form::text('title', $post->title, [
                        'id'    =>  'input_title',
                        'class' =>  'form-control',
                        'placeholder'   => 'Insert funny title here',
                        'required',
                        'oninput'   => "LimitInputLength(this, 'title_len', {$title_max_len})",
                        'value' => old('title'),
                    ]) }}
                    <p style="float:right;">(<span id="title_len">{{$title_max_len}}</span> characters left)</p>
                </div>
            </div>
            
            <!-- UPLOAD MEDIA -->
						
            <div class="form-group">
                <label for="file" class="col-md-4 control-label">Upload Media</label>

                <div class="col-md-6">
                    <input id="input_file" type="file" class="form-control" name="file" accept="{{Common::_desc['media_credentials_accept'][$default_media]}}">
                    <p style="float:right;" id="media_cred">{{Common::_desc['media_credentials'][$default_media]}}</p>
                </div>
            </div>

            <!-- MEDIA -->
            
            <div class="form-group">
                <label for="media" class="col-md-4 control-label">Media Type</label>

                <div class="col-md-3">
                    {{ Form::select('media', Common::_desc['media'], $post->media, [
                        'id'    => 'input_media',
                        'class' => 'form-control',
                        'value' => old('media'),
                        'required',
                    ]) }}
                </div>
            </div>

            <!-- DESCRIPTION -->

            <div class="form-group">
                <label for="description" class="col-md-4 control-label">Description</label>

                <div class="col-md-6">
                    {{ Form::textarea('description', $post->description, [
                        'id'    => 'input_description',
                        'class' => 'form-control',
                        'placeholder'   => 'Insert yoru hashtags here #HelloWorld',
                        'oninput'   => "LimitInputLength(this, 'desc_len', {$desc_max_len})",
                        'value' => old('description'),
                    ]) }}
                    <p style="float:right;">(<span id="desc_len">{{$desc_max_len}}</span> characters left)</p>
                </div>
            </div>

            <!-- SUBMIT BUTTON -->

            <div class="form-group">
                <div class="col-md-8 col-md-offset-4">
                    {!! Form::button('Update', [
                        'type' => 'submit',
                        'class' => 'btn btn-primary',
                    ]) !!}
                <a href= "{{ url( '/post/' . $post->id) }}" class="btn button_cancel"> Cancel </a>
                </div>

            </div>

        {!! Form::close() !!}
    </div>
    </div>
</div>

<script>

const media_creds = JSON.parse('<?php echo json_encode(Common::_desc["media_credentials"]) ?>');
const media_creds_accept = JSON.parse('<?php echo json_encode(Common::_desc["media_credentials_accept"]) ?>');

function LimitInputLength(html, disp_html, max_len)
{
    if (html.value.length <= max_len) document.getElementById(disp_html).innerHTML = max_len - html.value.length; 
    else {
        html.value = html.value.substr(0, max_len); 
        document.getElementById(disp_html).innerHTML = 0;
    }
}

$("#input_file").get(0).onchange = function() {
    if(this.files[0].size > 2 * 1024 * 1024 ) {
       alert("File is too big!");
       this.value = "";
    }
}

$("#input_media").get(0).onchange = function() {
    $("#media_cred").html(media_creds[this.value]);
    $("#input_file").val(''); 
    $("#input_file").prop('accept', media_creds_accept[this.value]);
}

</script>
@endsection



