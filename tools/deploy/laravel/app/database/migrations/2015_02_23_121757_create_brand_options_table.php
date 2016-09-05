<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBrandOptionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('brand_options', function(Blueprint $table)
		{
            $table->bigIncrements('id');
            $table->integer('brand_id')->unsigned();
            $table->integer('option_id')->unsigned();
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();
            $table->foreign('brand_id')->references('id')->on('brands');
		});

        DB::statement("INSERT INTO brand_options SELECT null as id, id as brand_id, 1 as option_id, 0 as del_flg, now() as created_at, now() as updated_at FROM brands WHERE del_flg = 0;");
        DB::statement("INSERT INTO brand_options SELECT null as id, id as brand_id, 2 as option_id, 0 as del_flg, now() as created_at, now() as updated_at FROM brands WHERE del_flg = 0;");
        DB::statement("INSERT INTO brand_options SELECT null as id, id as brand_id, 3 as option_id, 0 as del_flg, now() as created_at, now() as updated_at FROM brands WHERE del_flg = 0;");
        DB::statement("INSERT INTO brand_options SELECT null as id, id as brand_id, 4 as option_id, 0 as del_flg, now() as created_at, now() as updated_at FROM brands WHERE del_flg = 0;");
        DB::statement("INSERT INTO brand_options SELECT null as id, id as brand_id, 5 as option_id, 0 as del_flg, now() as created_at, now() as updated_at FROM brands WHERE del_flg = 0;");
        DB::statement("INSERT INTO brand_options SELECT null as id, id as brand_id, 6 as option_id, 0 as del_flg, now() as created_at, now() as updated_at FROM brands WHERE del_flg = 0;");
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('brand_options');
	}

}
