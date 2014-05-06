<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Init extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
	
		Schema::create('cinemas', function($table)
		{
			$table->increments('id');
			$table->string('name')->unique();
		});
		
		Schema::create('halls', function($table)
		{
			$table->increments('id');
			$table->integer('cinema_id')->unsigned()->index();
			$table->string('name');
			$table->foreign('cinema_id')->references('id')->on('cinemas')->onDelete('cascade');
			$table->unique(array('cinema_id', 'name'));
		});

		Schema::create('places', function($table)
		{
			$table->increments('id');
			$table->integer('hall_id')->unsigned()->index();
			$table->integer('num')->unsigned();
			$table->foreign('hall_id')->references('id')->on('halls')->onDelete('cascade');
			$table->unique(array('hall_id', 'num'));
		});

		Schema::create('movies', function($table)
		{
			$table->increments('id');
			$table->string('name')->unique();
		});
		
		Schema::create('schedules', function($table)
		{
			$table->increments('id');
			$table->integer('hall_id')->unsigned()->index();
			$table->integer('cinema_id')->unsigned()->index();
			$table->integer('movie_id')->unsigned()->index();
			$table->timestamp('begins_at')->index();
			$table->foreign('hall_id')->references('id')->on('halls')->onDelete('cascade');
			$table->foreign('movie_id')->references('id')->on('movies')->onDelete('cascade');
			$table->unique(array('hall_id', 'movie_id', 'begins_at')); //guarantees at this time in the hall is only one film
		});
		
		Schema::create('tickets', function($table)
		{
			$table->increments('id');
			$table->integer('schedule_id')->unsigned()->index();
			$table->integer('place_id')->unsigned()->index();
			$table->foreign('schedule_id')->references('id')->on('schedules');
			$table->foreign('place_id')->references('id')->on('places');
			$table->unique(array('schedule_id', 'place_id')); //guarantees that only one ticket sale
		});

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('cinemas');
		Schema::drop('halls');
		Schema::drop('places');
		Schema::drop('movies');
		Schema::drop('schedules');
		Schema::drop('tickets');
	}

}
