<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertBrandOption9 extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        DB::statement("INSERT INTO brand_options (brand_id, option_id, created_at, updated_at)
SELECT brand_id, 9, NOW(), NOW() FROM brand_options WHERE option_id = 5;");
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        DB::statement("DELETE FROM brand_options WHERE option_id = 9;");
	}

}
