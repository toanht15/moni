<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCpInstagramFollowActionEntries extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cp_instagram_follow_action_entries', function(Blueprint $table)
		{
			$table->increments('id');
            $table->integer('cp_instagram_follow_action_id')->unsigned();
            $table->integer('brand_social_account_id')->unsigned();
            $table->integer('instagram_entry_id')->unsigned();
            $table->timestamps();
            $table->foreign('cp_instagram_follow_action_id', 'instagram_follow_action_relation')->references('id')->on('cp_instagram_follow_actions');
            $table->foreign('brand_social_account_id', 'brand_social_account_relation')->references('id')->on('brand_social_accounts');
            $table->foreign('instagram_entry_id')->references('id')->on('instagram_entries');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('cp_instagram_follow_action_entries');
	}

}
