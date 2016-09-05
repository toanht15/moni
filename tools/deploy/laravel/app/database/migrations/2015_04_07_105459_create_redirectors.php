<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRedirectors extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('redirectors', function(Blueprint $table)
		{
			$table->increments('id');
            $table->integer('brand_id')->unsigned();
            $table->string('sign', 255)->default('');
            $table->string('url', 255)->default('');
            $table->text('description');
            $table->boolean('del_flg')->default(false);
            $table->timestamps();

            $table->foreign('brand_id')->references('id')->on('brands');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('redirectors');
	}

}
