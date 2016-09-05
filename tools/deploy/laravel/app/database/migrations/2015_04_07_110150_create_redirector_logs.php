<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRedirectorLogs extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('redirector_logs', function(Blueprint $table)
		{
			$table->increments('id');
            $table->integer('redirector_id')->unsigned();
            $table->bigInteger('user_id')->unsigned()->default(0);
            $table->integer('brand_id')->unsigned();
            $table->dateTime('login_date');
            $table->string('user_agent', 512)->default('');
            $table->string('referer_url', 512)->default('');
            $table->tinyInteger('device')->default(1);
            $table->string('ip_address', 30)->default('');
			$table->timestamps();
            $table->foreign('redirector_id')->references('id')->on('redirectors');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('redirector_logs');
	}

}
