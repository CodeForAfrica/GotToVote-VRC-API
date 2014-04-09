<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVotingCentersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('voting_centers', function(Blueprint $table)
		{
			//
			$table->increments('id');
			$table->integer('center_code')->default(0);
			$table->integer('ward_code')->default(0);
			$table->integer('constituency_code')->default(0);
			$table->integer('county_code')->default(0);
			$table->string('center_name');
			$table->string('ward_name')->default(0);
			$table->string('constituency_name');
			$table->string('county_name');
			$table->string('region');
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
		Schema::dropIfExists('voting_centers');
	}

}