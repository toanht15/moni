<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBrandContracts extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('brand_contracts', function(Blueprint $table)
		{
			//
            $table->increments('id');
            $table->unsignedInteger('brand_id');
            $table->dateTime('contract_start_date')->default('1970-01-01 00:00:00');
            $table->dateTime('contract_end_date')->default('9999-12-31 23:59:59');
            $table->string('closed_title');
            $table->text('closed_description');
            $table->boolean('delete_status')->default(0);
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();

            $table->foreign('brand_id')->references('id')->on('brands');
            $table->unique('brand_id');
		});

        DB::statement("INSERT INTO brand_contracts(brand_id) SELECT id FROM brands WHERE del_flg = 0;");
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('brand_contracts', function(Blueprint $table)
		{
			//
            $table->drop();
		});
	}

}
