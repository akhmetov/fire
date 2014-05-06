<?php

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();
		
		DB::connection()->disableQueryLog();
		
		$faker = Faker\Factory::create();
		
		foreach(range(0, 7) as $movie_id) {
			$movie = new Movie;
			$movie->name = "{$faker->lastName}'s Penguins";
			$movie->save();
		}
				
		foreach(range(1, 3) as $cinema_num) {
			$cinema = new Cinema;
			$cinema->name = "{$faker->name}'s Theatre";
			$cinema->save();
			foreach(range(1, rand(3,8)) as $hall_num) {
				$hall = new Hall;
				$hall->name = $hall_num > 3 ? "VIP #".($hall_num - 3) : "Hall #{$hall_num}";
				$hall->cinema()->associate($cinema);
				$hall->save();
				foreach(range(1, $hall_num > 3 ? rand(12, 36) : rand(100, 200)) as $place_num) {
					$place = new Place;
					$place->num = $place_num;
					$place->hall()->associate($hall);
					$place->save();
				}
				$movie = Movie::find($hall_num);
				foreach(range(time() - date('i') * 60 - date('s') - 3 * 24 * 3600, time() + 7 * 24 * 3600, 120 * 60) as $begins_at) {
					$schedule = new Schedule;
					$schedule->begins_at = date('Y-m-d H:i:s', $begins_at - $movie->id * 24 * 3600);
					$schedule->cinema()->associate($cinema);
					$schedule->hall()->associate($hall);
					$schedule->movie()->associate($movie);
					$schedule->save();
					if($begins_at < time() + 8 * 3600) {
						foreach($hall->places as $place) {
							if(rand(0, 1) * rand(0, 1)) {
								$ticket = new Ticket;
								$ticket->schedule()->associate($schedule);
								$ticket->place()->associate($place);
								$ticket->save();
							}
						}
					}
				}
			}
		}

	}

	}
