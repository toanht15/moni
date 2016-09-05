<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTestPageForBrands extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('brands', function(Blueprint $t)
        {
            $t->string('test_page')->default('0')->after('background_img_y');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('brands', function(Blueprint $t)
        {
            $t->dropColumn('test_page');
        });
	}

}
