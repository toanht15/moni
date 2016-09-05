<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSnsActionCountLogsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sns_action_count_logs', function(Blueprint $table)
        {
            $table->bigIncrements('id');
            $table->integer('social_app_id')->unsigned();
            $table->string('sns_uid');
            $table->string('social_media_account_id');
            $table->tinyInteger('log_type');
            $table->integer('action_count')->unsigned();
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();

            $table->unique(array('sns_uid','social_media_account_id','log_type','social_app_id'), 'sns_action_count_logs_unique_key');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('sns_action_count_logs');
    }

}
