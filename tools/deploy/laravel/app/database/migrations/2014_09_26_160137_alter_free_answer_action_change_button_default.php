<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterFreeAnswerActionChangeButtonDefault extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        DB::statement('ALTER TABLE `cp_free_answer_actions` MODIFY  `button_label` varchar(255) NOT NULL DEFAULT "回答する"');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        DB::statement('ALTER TABLE `cp_free_answer_actions` MODIFY  `button_label` varchar(255) NOT NULL DEFAULT "送信する"');
	}

}
