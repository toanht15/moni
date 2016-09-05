<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMoniplaEngagementLog extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('monipla_engagement_logs', function(Blueprint $table)
		{
			$table->bigIncrements('id');
			$table->tinyInteger('social_media_id');
			$table->string('locate_id');
			$table->tinyInteger('value');
			$table->unsignedBigInteger('user_id');
			$table->dateTime('clicked_at');

			$table->index(array('clicked_at', 'social_media_id'));
			$table->index(array('clicked_at', 'social_media_id','locate_id'),'clicked_at_social_media_id_locate_id_index');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('monipla_engagement_logs');
	}

}
