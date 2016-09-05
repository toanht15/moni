<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStaticHtmlEntryUsers extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('static_html_entry_users', function(Blueprint $table)
		{
			$table->bigIncrements('id');
            $table->unsignedInteger('static_html_entry_id')->default(0);
            $table->unsignedBigInteger('user_id')->default(0);
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('static_html_entry_id')->references('id')->on('static_html_entries');

            $table->index('static_html_entry_id');
            $table->index('user_id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('static_html_entry_users');
	}

}
