<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSmsesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('smses', function(Blueprint $table)
		{
			//
			$table->increments('id');
			$table->string('session_id');
			$table->string('message_id');
			$table->string('message_body');
			$table->string('date_received');
			$table->string('message_type');
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
		Schema::drop('smses');
	}

}