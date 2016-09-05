<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMultiPostSnsQueues extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('multi_post_sns_queues', function(Blueprint $table)
		{
			$table->bigIncrements('id');
            $table->unsignedInteger('social_media_type')->default(0);
            $table->string('social_account_id', 255)->default('');
            $table->string('access_token', 511)->default('');
            $table->string('access_refresh_token', 511)->default('');
            $table->string('share_text', 255)->default('');
            $table->string('share_image_url', 511)->default('');
            $table->string('share_url', 511)->default('');
            $table->string('share_title', 255)->default('');
            $table->string('share_caption', 255)->default('');
            $table->string('share_description', 255)->default('');
            $table->unsignedInteger('callback_function_type')->default(0);
            $table->integer('callback_parameter')->default(0);
            $table->boolean('error_flg')->default(0);
            $table->boolean('del_flg')->default(0);
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
		Schema::drop('multi_post_sns_queues');
	}

}
