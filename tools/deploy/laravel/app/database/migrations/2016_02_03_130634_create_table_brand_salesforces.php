<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableBrandSalesforces extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('brand_salesforces', function(Blueprint $table)
		{
			$table->increments('id');
            $table->integer('brand_id')->unsigned();
            $table->string('url',255)->default('');
            $table->date('start_date');
            $table->date('end_date');
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
		Schema::drop('brand_salesforces');
	}

}
