<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PostLike extends Model
{
	public $timestamps = false;
	public $incrementing = false;
	public $primaryKey = ['post_id', 'user_id'];
    protected $fillable = [
		'post_id',
		'user_id',
		'status',
	];
	
	public function post()
	{
		return $this->belongsTo("App\Post");
	}
	
	public function user()
	{
		return $this->belongsTo("App\User");
	}


	//
	// Code was retrieved from
	// https://laracasts.com/discuss/channels/general-discussion/how-to-reference-composite-key-in-models
	// Retrieved at 13 MAR 2020 

	protected function getKeyForSaveQuery()
	{

		$primaryKeyForSaveQuery = array(count($this->primaryKey));

		foreach ($this->primaryKey as $i => $pKey) {
			$primaryKeyForSaveQuery[$i] = isset($this->original[$this->getKeyName()[$i]])
				? $this->original[$this->getKeyName()[$i]]
				: $this->getAttribute($this->getKeyName()[$i]);
		}

		return $primaryKeyForSaveQuery;

	}

	/**
	 * Set the keys for a save update query.
	 *
	 * @param  \Illuminate\Database\Eloquent\Builder  $query
	 * @return \Illuminate\Database\Eloquent\Builder
	 */
	protected function setKeysForSaveQuery(Builder $query)
	{

		foreach ($this->primaryKey as $i => $pKey) {
			$query->where($this->getKeyName()[$i], '=', $this->getKeyForSaveQuery()[$i]);
		}

		return $query;
	}
	
}
