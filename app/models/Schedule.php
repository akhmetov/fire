<?php

class Schedule extends Eloquent {

	public $timestamps = false;
	
	protected $hidden = array('hall_id', 'cinema_id', 'movie_id');
	
	public function cinema()
	{
		return $this->belongsTo('Cinema');
	}
	
	public function hall()
	{
		return $this->belongsTo('Hall');
	}
	
	public function movie()
	{
		return $this->belongsTo('Movie');
	}

	public function tickets()
	{
		return $this->hasMany('Ticket');
	}
	
	/*
	 * SELECT
	 *   `places`.`num`
	 * FROM
	 *   `schedules`
	 * INNER JOIN
	 *   `halls` ON `schedules`.`hall_id` = `halls`.`id`
	 * INNER JOIN
	 *   `places` ON `halls`.`id` = `places`.`hall_id`
	 * WHERE
	 *   `schedules`.`id` = ?
	 *   AND NOT EXISTS (
	 *     SELECT
	 *       1
	 *     FROM
	 *       `tickets`
	 *     WHERE
	 *       tickets.place_id = places.id
     *       AND
	 *       tickets.schedule_id = schedules.id
	 *   )
	 */
	public function scopePlaces($query)
	{
		return $query
				->join('halls', 'schedules.hall_id', '=', 'halls.id')
				->join('places', 'halls.id', '=', 'places.hall_id')
				->whereNotExists(function($query)
					{
						$query->select(DB::raw(1))
							  ->from('tickets')
							  ->whereRaw('tickets.place_id = places.id')
							  ->whereRaw('tickets.schedule_id = schedules.id');
					})
				->select('places.num');
	}
	
	public function scopeUpcoming($query) {
		return $query->where('begins_at', '>', date('Y-m-d H:i:s'));
	}

}
