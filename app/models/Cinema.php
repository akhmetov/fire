<?php

class Cinema extends Eloquent {

	public $timestamps = false;

	public function halls()
	{
		return $this->hasMany('Hall')->orderBy('name');
	}

}
