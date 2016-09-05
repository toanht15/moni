<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentTables extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('brand_login_settings', function(Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('brand_id');
            $table->integer('social_media_id');
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();

            $table->foreign('brand_id')->references('id')->on('brands');
            $table->index(array('brand_id', 'social_media_id'));
        });

        Schema::create('comment_plugins', function(Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('brand_id');
            $table->string('title');
            $table->text('free_text');
            $table->tinyInteger('type')->default(1);
            $table->tinyInteger('status')->default(1);
            $table->tinyInteger('active_flg')->default(0);
            $table->tinyInteger('login_limit_flg')->default(1);
            $table->unsignedInteger('static_html_entry_id');
            $table->string('plugin_code');
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();

            $table->foreign('brand_id')->references('id')->on('brands');
        });

        Schema::create('comment_plugin_share_settings', function(Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('comment_plugin_id');
            $table->integer('social_media_id');
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();

            $table->foreign('comment_plugin_id')->references('id')->on('comment_plugins');
            $table->index(array('comment_plugin_id', 'social_media_id'), 'cp_share_settings_comment_plugin_id_social_media_id_index');
        });

        Schema::create('comment_plugin_actions', function(Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('comment_plugin_id');
            $table->integer('order_no');
            $table->tinyInteger('requirement_flg')->default(0);
            $table->tinyInteger('type')->default(1);
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();

            $table->foreign('comment_plugin_id')->references('id')->on('comment_plugins');
        });

        Schema::create('comment_free_text_actions', function(Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('comment_plugin_action_id');
            $table->string('text');
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();

            $table->foreign('comment_plugin_action_id')->references('id')->on('comment_plugin_actions');
        });

        Schema::create('anonymous_users', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 40);
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();
        });

        Schema::create('comment_users', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('comment_plugin_id');
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();

            $table->foreign('comment_plugin_id')->references('id')->on('comment_plugins');
            $table->index('comment_plugin_id');
        });

        Schema::create('comment_free_text_users', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('comment_user_id');
            $table->unsignedInteger('comment_action_id');
            $table->text('text');
            $table->text('extra_data');
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();

            $table->foreign('comment_user_id')->references('id')->on('comment_users');
            $table->foreign('comment_action_id')->references('id')->on('comment_free_text_actions');
            $table->index(array('comment_user_id', 'comment_action_id'));
        });

        Schema::create('comment_user_replies', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('comment_user_id');
            $table->text('text');
            $table->text('extra_data');
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();

            $table->foreign('comment_user_id')->references('id')->on('comment_users');
            $table->index('comment_user_id');
        });


        Schema::create('comment_user_relations', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('object_id');
            $table->tinyInteger('object_type');
            $table->bigInteger('no');
            $table->unsignedBigInteger('user_id');
            $table->tinyInteger('anonymous_flg')->default(0);
            $table->text('note');
            $table->string('request_url');
            $table->tinyInteger('status')->default(1);
            $table->tinyInteger('discard_flg')->default(0);
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();

            $table->index(array('object_id', 'object_type'), 'comment_user_relation_comment_index_key');
            $table->index(array('user_id', 'anonymous_flg'), 'comment_user_relation_user_index_key');
        });

        Schema::create('comment_user_shares', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('comment_user_relation_id');
            $table->integer('social_media_id');
            $table->tinyInteger('execute_status')->default(0);
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();

            $table->index('comment_user_relation_id');
        });

        Schema::create('comment_user_likes', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('comment_user_relation_id');
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->index(array('comment_user_relation_id', 'user_id'));
        });

        Schema::create('comment_user_hidden_logs', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('comment_user_relation_id');
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->index(array('comment_user_relation_id', 'user_id'));
        });

        Schema::create('comment_user_mentions', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('comment_user_relation_id');
            $table->unsignedBigInteger('mentioned_user_id');
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();

            $table->index(array('comment_user_relation_id', 'mentioned_user_id'), 'comment_user_mention_index_key');
        });

        Schema::create('user_public_profile_info', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->string('nickname', 40);
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
        });

        Schema::create('comments_users_max_relation_no', function(Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('comment_plugin_id');
            $table->integer('max_no');

            $table->foreign('comment_plugin_id')->references('id')->on('comment_plugins');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('comments_users_max_relation_no');
        Schema::drop('comment_user_mentions');
        Schema::drop('comment_user_likes');
        Schema::drop('comment_user_shares');
        Schema::drop('comment_user_hidden_logs');
        Schema::drop('comment_free_text_users');
        Schema::drop('comment_user_relations');
        Schema::drop('comment_user_replies');
        Schema::drop('comment_users');
        Schema::drop('anonymous_users');
        Schema::drop('comment_free_text_actions');
        Schema::drop('comment_plugin_actions');
        Schema::drop('comment_plugin_share_settings');
        Schema::drop('comment_plugins');
        Schema::drop('brand_login_settings');
        Schema::drop('user_public_profile_info');
    }

}
