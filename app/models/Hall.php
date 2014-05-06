<?php

class Hall extends Eloquent {

	public $timestamps = false;
	
	protected $hidden = array('cinema_id');
	
	public function cinema()
	{
		return $this->belongsTo('Cinema');
	}

	public function places()
	{
		return $this->hasMany('Place')->orderBy('num');
	}
	
}
