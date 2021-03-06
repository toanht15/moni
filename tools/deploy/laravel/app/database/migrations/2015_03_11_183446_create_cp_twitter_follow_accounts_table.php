<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCpTwitterFollowAccountsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cp_twitter_follow_accounts', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('action_id')->unsigned();
            $table->integer('brand_social_account_id')->unsigned();
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();
            $table->foreign('action_id')->references('id')->on('cp_twitter_follow_actions');
            $table->foreign('brand_social_account_id')->references('id')->on('brand_social_accounts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('cp_twitter_follow_accounts');
    }
}
