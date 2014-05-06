<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::group(array('prefix' => 'api'), function()
{

	/**
	 * List of the theatres.
	 * @return [{"id": "1", "name": "Theater"}, ...]
	 */
	Route::get('cinema/list', function()
	{	
		$cinemas = Cinema::all();
		return Response::json($cinemas);
	});

	/**
	 * Upcoming sessions of the given theatre.
	 * @return [{
	 *           "id": "1", //session_id
	 *           "begins_at": "2014-05-05 23:00:00", //datetime
	 *           "movie": {"id": "1", "name": "Penguins"},
	 *           "hall": {"id": "1", "name": "#1"}
	 *           },
	 *          ...]
	 */
	Route::get('cinema/{cinema}/schedule', function($cinema)
	{	
		$scope = Schedule::where('cinema_id', '=', $cinema);
		if (Input::has('hall'))
		{
			$scope = $scope->where('hall_id', '=', Input::get('hall'));
		}
		$sessions = $scope->with('movie', 'hall')
						->upcoming()
						->get();
		return Response::json($sessions);
	});

	/**
	 * List of the upcoming movies. NOT JUST ALL MOVIES FROM DB, ONLY FROM TODAY AND FURTHER.
	 * @return [{"id": "1", "name": "Penguins"}, ...]
	 */
	Route::get('film/list', function()
	{	
		$movies = Movie::upcoming()->get();
		return Response::json($movies);
	});

	/**
	 * Upcoming sessions of the given movie.
	 * @return [{
	 *           "id": "1", //session_id
	 *           "begins_at": "2014-05-05 23:00:00", //datetime
	 *           "cinema": {"id": "1", "name": "Theater"},
	 *           "hall": {"id": "1", "name": "#1"}
	 *           },
	 *          ...]
	 */
	Route::get('film/{movie}/schedule', function($movie)
	{
		$sessions = Schedule::where('movie_id', '=', $movie)
						->with('hall', 'cinema')
						->upcoming()
						->get();
		return Response::json($sessions);
	});

	/**
	 * List of the available places for the given session.
	 * @return ["1", ...]
	 */
	Route::get('session/{session}/places', function($session)
	{
		$places = Schedule::where('schedules.id', '=', $session)
						->places() // <- logic in model (/app/models/Schedule.php)
						->lists('num');
		return Response::json($places);
	});

	/**
	 * Buy action.
	 * @return ["1", ...]
	 */
	Route::post('tickets/buy', function()
	{
		$places = explode(',', Input::get('places'));
		$empty = Schedule::where('schedules.id', '=', Input::get('session'))
						->places()
						->whereIn('num', $places)
						->get();
		if(sizeof($places) == sizeof($empty)) {		
			$codes = array();
			foreach ($empty as $place)
			{
				$ticket = new Ticket;
				$ticket->schedule_id = Input::get('session');
				$ticket->place_id = $place->id;
				$ticket->save();
				$codes[] = Crypt::encrypt($ticket->id);
			}
			return Response::json($codes);
		} else {
			return Response::json(array('error' => 'Some of the places already sold'), 404);		
		}
	});

	/**
	 * Reject action.
	 * @return {"delete": "ok"}
	 */
	Route::post('tickets/reject/{ticket}', function($ticket)
	{
		$ticket = Ticket::find(Crypt::decrypt($ticket));
		if($ticket) {
			$deadline = Schedule::find($ticket->schedule_id)
						->begins_at
						->subHour();
			if(time() < $deadline->timestamp) {
				if($ticket->delete())
				{
					return Response::json(array('delete' => 'ok'));
				} else {
					App::abort(404);
				}
			} else {
				return Response::json(array('error' => 'Deadline for rejecting was at '.$deadline), 404);
			}
		}
	});

});

App::missing(function($exception)
{
    return Response::json(array('error' => 'not found'), 404);
});