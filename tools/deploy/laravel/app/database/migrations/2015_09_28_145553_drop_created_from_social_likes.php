<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropCreatedFromSocialLikes extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('social_likes', function(Blueprint $table)
		{
			$table->dropColumn('created');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('social_likes', function(Blueprint $table)
		{
			$table->bigInteger('created');
		});
	}

}
