<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWebAccessTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('web_access', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('voter_id');
			$table->string('ip_address', 39)->default('0');
			$table->boolean('success');
			$table->integer('user_id')->default(0);
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
		Schema::drop('web_access');
	}

}
