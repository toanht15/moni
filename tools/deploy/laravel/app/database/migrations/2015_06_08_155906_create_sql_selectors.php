<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSqlSelectors extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sql_selectors', function(Blueprint $table)
		{
            $table->increments('id');

            $table->string('title', 32)->default('');
            $table->string('description', 255)->default('');
            $table->longText('sql_string');
            $table->string('db_name', 32)->default('');
            $table->integer('status')->unsigned();
            $table->string('author', 32)->default('');

            $table->tinyInteger('del_flg')->default(0);
            // created_at, updated_at DATETIME
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
        Schema::drop('sql_selectors');
	}

}
