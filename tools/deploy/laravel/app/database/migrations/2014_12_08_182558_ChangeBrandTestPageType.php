<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeBrandTestPageType extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::statement('ALTER TABLE brands MODIFY COLUMN test_page TINYINT(4) DEFAULT 0 NOT NULL');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement('ALTER TABLE brands MODIFY COLUMN test_page VARCHAR(255) DEFAULT 0 NOT NULL');
	}

}
