<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCompositeIndexToSocialLikes extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('social_likes', function(Blueprint $table)
		{
			$table->dropIndex("social_likes_user_id_index");
			$table->index(array("user_id", "like_id"));
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
			$table->dropIndex("social_likes_user_id_like_id_index");
			$table->index(array("user_id"));
		});
	}

}
