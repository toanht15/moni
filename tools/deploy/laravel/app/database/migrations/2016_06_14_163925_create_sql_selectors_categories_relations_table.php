<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSqlSelectorsCategoriesRelationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sql_selectors_categories_relations', function(Blueprint $table)
		{
			$table->increments('id');
            $table->integer('sql_selector_id')->unsigned();
            $table->integer('sql_category_id')->unsigned();
            $table->boolean('del_flg')->default(0);

            // created_at, updated_at DATETIME
            $table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('sql_selectors_categories_relations');
	}

}
