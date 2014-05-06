<?php

class Place extends Eloquent {

	public $timestamps = false;
	
	public function hall()
	{
		return $this->belongsTo('Hall');
	}
	
}
