<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWithdrawReasonLog extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('withdraw_reasons_logs', function(Blueprint $t) {
            // auto increment id (primary key)
            $t->bigIncrements('id');
            $t->bigInteger('withdraw_log_id')->unsigned();
            $t->text('reason');
            $t->tinyInteger('question_num')->default(1);
            $t->boolean('del_flg')->default(false);
            // created_at, updated_at DATETIME
            $t->timestamps();
            $t->index('withdraw_log_id');
            $t->foreign('withdraw_log_id')->references('id')->on('withdraw_logs');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::drop('withdraw_reasons_logs');
	}

}
