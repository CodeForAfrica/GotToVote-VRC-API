<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVotingCentresTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('voting_centres', function(Blueprint $table)
		{
			//
			$table->increments('id');
			$table->integer('centre_code');
			$table->integer('ward_code');
			$table->integer('constituency_code');
			$table->integer('county_code');
			$table->string('centre_name');
			$table->string('ward_name');
			$table->string('constituency_name');
			$table->string('county_name');
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('voting_centres');
	}

}