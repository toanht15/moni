<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeUserMessagesTables extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::drop('user_campaign_messages');
        Schema::drop('user_campaign_threads');
        Schema::drop('user_campaign_action_statuses');
        Schema::drop('campaign_message_masters');


        Schema::create('user_campaign_threads', function(Blueprint $table)
        {
            $table->increments('id');
            $table->bigInteger('user_id')->unsigned();
            $table->integer('campaign_id')->unsigned();
            $table->string('title');
            $table->tinyInteger('total_message_count')->default(0);
            $table->tinyInteger('unread_message_count')->default(0);
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('campaign_id')->references('id')->on('campaigns');
            $table->unique(array('user_id', 'campaign_id'));
        });

        Schema::create('user_campaign_messages', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('user_campaign_thread_id')->unsigned();
            $table->integer('campaign_action_id')->unsigned();
            $table->longText('contents');
            $table->tinyInteger('read_flg')->default(0);
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();
            $table->foreign('user_campaign_thread_id')->references('id')->on('user_campaign_threads');
            $table->foreign('campaign_action_id')->references('id')->on('campaign_actions');
            $table->unique(array('user_campaign_thread_id', 'campaign_action_id'), 'user_campaign_thread_id_campaign_action_id_unique');
        });

        Schema::create('user_campaign_joined_statuses', function(Blueprint $table)
        {
            $table->increments('id');
            $table->bigInteger('user_id')->unsigned();
            $table->integer('campaign_id')->unsigned();
            $table->tinyInteger('status')->default(0);
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('campaign_id')->references('id')->on('campaigns');
            $table->unique(array('user_id', 'campaign_id'));
        });


        Schema::create('user_campaign_action_statuses', function(Blueprint $table)
        {
            $table->increments('id');
            $table->bigInteger('user_id')->unsigned();
            $table->integer('campaign_action_id')->unsigned();
            $table->tinyInteger('status')->default(0);
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('campaign_action_id')->references('id')->on('campaign_actions');
            $table->unique(array('user_id', 'campaign_action_id'));
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{

        Schema::drop('user_campaign_action_statuses');
        Schema::drop('user_campaign_joined_statuses');
        Schema::drop('user_campaign_messages');
        Schema::drop('user_campaign_threads');

        Schema::create('campaign_message_masters', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('campaign_id')->unsigned();
            $table->integer('campaign_action_id')->unsigned();
            $table->longText('contents');
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();
            $table->foreign('campaign_id')->references('id')->on('campaigns');
            $table->foreign('campaign_action_id')->references('id')->on('campaign_actions');
            $table->unique(array('campaign_id', 'campaign_action_id'), 'u_campaign_id');
        });

        Schema::create('user_campaign_threads', function(Blueprint $table)
        {
            $table->increments('id');
            $table->bigInteger('user_id')->unsigned();
            $table->integer('campaign_id')->unsigned();
            $table->string('title');
            $table->tinyInteger('total_message_count')->default(0);
            $table->tinyInteger('unread_message_count')->default(0);
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('campaign_id')->references('id')->on('campaigns');
            $table->unique(array('campaign_id', 'user_id'));
        });

        Schema::create('user_campaign_messages', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('user_campaign_thread_id')->unsigned();
            $table->integer('campaign_message_master_id')->unsigned();
            $table->longText('contents');
            $table->tinyInteger('read_flg')->default(0);
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();
            $table->foreign('user_campaign_thread_id')->references('id')->on('user_campaign_threads');
            $table->foreign('campaign_message_master_id')->references('id')->on('campaign_message_masters');
            $table->unique(array('user_campaign_thread_id', 'campaign_message_master_id'), 'u_user_campaign_thread_id');
        });

        Schema::create('user_campaign_action_statuses', function(Blueprint $table)
        {
            $table->increments('id');
            $table->bigInteger('user_id')->unsigned();
            $table->integer('campaign_action_id')->unsigned();
            $table->tinyInteger('status')->default(0);
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('campaign_action_id')->references('id')->on('campaign_actions');
            $table->unique(array('user_id', 'campaign_action_id'));
        });
	}

}
