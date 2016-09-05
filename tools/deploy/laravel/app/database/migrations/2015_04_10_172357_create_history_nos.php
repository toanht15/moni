<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHistoryNos extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('history_nos', function(Blueprint $table)
		{
			$table->increments('id');
			$table->unsignedInteger('questionnaires_questions_relation_id');
			$table->unsignedBigInteger('brands_users_relation_id');
			$table->smallInteger('history_count')->default(0);
			$table->timestamps();
			$table->foreign('questionnaires_questions_relation_id')->references('id')->on('profile_questionnaires_questions_relations');
			$table->foreign('brands_users_relation_id')->references('id')->on('brands_users_relations');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('history_nos');
	}

}
