<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCpUsersTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {

        Schema::drop('cp_user_action_statuses');
        Schema::drop('cp_user_messages');
        Schema::drop('cp_user_threads');


        Schema::create('cp_users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cp_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();
            $table->foreign('cp_id')->references('id')->on('cps');
            $table->foreign('user_id')->references('id')->on('users');
            $table->unique(array('cp_id', 'user_id'));
        });

        Schema::create('cp_user_action_messages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cp_user_id')->unsigned();
            $table->integer('cp_action_id')->unsigned();
            $table->tinyInteger('read_flg')->default(0);
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();
            $table->foreign('cp_user_id')->references('id')->on('cp_users');
            $table->foreign('cp_action_id')->references('id')->on('cp_actions');
            $table->unique(array('cp_user_id', 'cp_action_id'));
        });

        Schema::create('cp_user_action_statuses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cp_user_id')->unsigned();
            $table->integer('cp_action_id')->unsigned();
            $table->tinyInteger('status')->default(0);
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();
            $table->foreign('cp_user_id')->references('id')->on('cp_users');
            $table->foreign('cp_action_id')->references('id')->on('cp_actions');
            $table->unique(array('cp_user_id', 'cp_action_id'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {

        Schema::drop('cp_user_action_statuses');
        Schema::drop('cp_user_action_messages');
        Schema::drop('cp_users');

        Schema::create('cp_user_threads', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cp_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();
            $table->string('title');
            $table->tinyInteger('total_message_count')->default(0);
            $table->tinyInteger('unread_message_count')->default(0);
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();
            $table->foreign('cp_id')->references('id')->on('cps');
            $table->foreign('user_id')->references('id')->on('users');
            $table->unique(array('cp_id', 'user_id'));
        });


        Schema::create('cp_user_messages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cp_user_thread_id')->unsigned();
            $table->integer('cp_action_id')->unsigned();
            $table->longText('contents');
            $table->tinyInteger('read_flg')->default(0);
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();
            $table->foreign('cp_user_thread_id')->references('id')->on('cp_user_threads');
            $table->foreign('cp_action_id')->references('id')->on('cp_actions');
            $table->unique(array('cp_user_thread_id', 'cp_action_id'));
        });

        Schema::create('cp_user_action_statuses', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('user_id')->unsigned();
            $table->integer('cp_action_id')->unsigned();
            $table->tinyInteger('status')->default(0);
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('cp_action_id')->references('id')->on('cp_actions');
            $table->unique(array('user_id', 'cp_action_id'));
        });

    }

}
