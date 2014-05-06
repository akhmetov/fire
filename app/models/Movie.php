<?php

class Movie extends Eloquent {

	public $timestamps = false;
	
	public function schedules()
	{
		return $this->hasMany('Schedule');
	}
	
	/*
	* SELECT * FROM `movies` WHERE (SELECT COUNT(*) FROM `schedules` WHERE `schedules`.`movie_id` = `movies`.`id` AND `begins_at` > ?) >= 1
	*/
	public function scopeUpcoming($query)
	{
		return $query->whereHas('schedules', function($query)
		{
			$query->where('begins_at', '>', date('Y-m-d H:i:s'));
		});
	}

}
