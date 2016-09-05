<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePhotoUserShares extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('photo_user_shares', function(Blueprint $table)
		{
			$table->increments('id');
            $table->unsignedBigInteger('photo_user_id');
            $table->unsignedInteger('social_media_type')->default(0);
            $table->foreign('photo_user_id')->references('id')->on('photo_users');
            $table->string('share_text', 511)->default('');
            $table->tinyInteger('execute_status')->default(0);
            $table->boolean('del_flg')->default(0);
            $table->unique(array('photo_user_id', 'social_media_type'));
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
		Schema::drop('photo_user_shares');
	}

}
