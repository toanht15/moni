<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableSnsPanelApiCodes extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sns_panel_api_codes', function(Blueprint $table){
			$table->increments('id');
			$table->integer('brand_id')->unsigned();
			$table->string('code');
			$table->text('extra_data');

			$table->tinyInteger('del_flg')->default(0);
			$table->timestamps();

			$table->index('brand_id');
			$table->foreign('brand_id')->references('id')->on('brands');
			$table->unique('code');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('sns_panel_api_codes');
	}

}
