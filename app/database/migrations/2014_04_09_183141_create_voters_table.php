<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVotersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('voters', function(Blueprint $table)
		{
			//
			$table->increments('id');
			$table->integer('voter_id')->default(0);
			$table->integer('center_code')->default(0);
			$table->string('surname')->default(0);
			$table->string('firstname')->default(0);
			$table->string('gender')->default(0);
			$table->string('dob')->default(0);
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
		Schema::drop('voters');
	}

}
