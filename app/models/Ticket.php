<?php

class Ticket extends Eloquent {

	public $timestamps = false;
	
	public function schedule()
	{
		return $this->belongsTo('Schedule', 'schedule_id');
	}
	
	public function place()
	{
		return $this->belongsTo('Place', 'place_id');
	}
	
}
