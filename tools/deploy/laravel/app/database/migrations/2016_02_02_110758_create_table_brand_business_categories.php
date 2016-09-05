<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableBrandBusinessCategories extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('brand_business_categories', function(Blueprint $table)
		{
			$table->bigIncrements('id');
            $table->integer('brand_id')->unsigned();
            $table->integer('category')->default(0);
            $table->integer('size')->unsigned();
            $table->boolean('del_flg')->default(0);
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
		Schema::drop('brand_business_categories');
	}

}
