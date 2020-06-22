<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class PostResource extends Resource
{
    public function toArray($request)
    {
        return [
			'title'			=> $this->id,
			'user_id'		=> $this->user_id,
			'description'	=> $this->description,
			'media'			=> $this->media,
			'view'			=> $this->view,
			'like'			=> $this->likeCount(),
			'duslike'		=> $this->dislikeCount(),
			
			// Want to display the comments
			
			'created_at'	=> $this->created_at,
			'updated_at'	=> $this->updated_at,
		]
    }
}
