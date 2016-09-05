<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInstagramHashtag extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cp_instagram_hashtag_actions', function(Blueprint $table)
		{
			$table->increments('id');
            $table->unsignedInteger('cp_action_id');
            $table->string('title', 255)->default('');
            $table->string('image_url', 511)->default('');
            $table->text('text');
            $table->text('html_content');
            $table->string('button_label_text', 255);
            $table->boolean('skip_flg')->default(0);
            $table->boolean('autoload_flg')->default(1);
            $table->boolean('approval_flg')->default(0);
            $table->boolean('del_flg')->default(0);
			$table->timestamps();

            $table->foreign('cp_action_id')->references('id')->on('cp_actions');
            $table->unique('cp_action_id');
		});

        Schema::create('cp_instagram_hashtags', function(Blueprint $table)
        {
            $table->increments('id');
            $table->unsignedInteger('cp_instagram_hashtag_action_id');
            $table->string('hashtag', 255)->default('');
            $table->text('pagination');
            $table->integer('total_media_count_start')->default(0);
            $table->integer('total_media_count_end')->default(0);
            $table->integer('cp_media_count_summary')->default(0);
            $table->boolean('del_flg')->default(0);
            $table->timestamps();

            $table->foreign('cp_instagram_hashtag_action_id', 'cp_insta_tag_action_id')->references('id')->on('cp_instagram_hashtag_actions');
            $table->unique(array('cp_instagram_hashtag_action_id', 'hashtag'), 'hashtag_unique');
        });

        Schema::create('instagram_hashtag_users', function(Blueprint $table)
        {
            $table->increments('id');
            $table->unsignedInteger('cp_action_id');
            $table->unsignedInteger('cp_user_id');
            $table->string('instagram_user_name', 255)->default('');
            $table->boolean('duplicate_flg');
            $table->boolean('del_flg');
            $table->timestamps();

            $table->foreign('cp_action_id')->references('id')->on('cp_actions');
            $table->foreign('cp_user_id')->references('id')->on('cp_users');
            $table->index(array('cp_action_id', 'instagram_user_name'));
        });

        Schema::create('instagram_hashtag_user_posts', function(Blueprint $table)
        {
            $table->increments('id');
            $table->unsignedInteger('instagram_hashtag_user_id');
            $table->string('object_id', 255)->default('');
            $table->string('link', 511)->default('');
            $table->string('type', 511)->default('');
            $table->string('user_name', 255)->default('');
            $table->string('user_account_id', 255)->default('');
            $table->string('post_text')->text();
            $table->string('low_resolution', 511)->default('');
            $table->string('thumbnail', 511)->default('');
            $table->string('standard_resolution', 511)->default('');
            $table->text('detail_data');
            $table->tinyInteger('approval_status')->default(0);
            $table->boolean('reverse_post_time_flg');
            $table->boolean('del_flg');
            $table->timestamps();

            $table->unique(array('instagram_hashtag_user_id', 'object_id'),'hashtag_user_unique');
            $table->foreign('instagram_hashtag_user_id', 'insta_tag_user_id')->references('id')->on('instagram_hashtag_users');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::drop('cp_instagram_hashtags');
		Schema::drop('cp_instagram_hashtag_actions');
        Schema::drop('instagram_hashtag_user_posts');
        Schema::drop('instagram_hashtag_users');
	}

}
