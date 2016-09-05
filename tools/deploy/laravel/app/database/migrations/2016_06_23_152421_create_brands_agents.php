<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBrandsAgents extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('brands_agents', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('manager_id');
			$table->integer('brand_id');
			$table->boolean('del_flg')->default(0);
			$table->timestamps();

            $table->unique(['manager_id', 'brand_id'], 'manager_brand_unique_key');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('brands_agents');
	}

}
