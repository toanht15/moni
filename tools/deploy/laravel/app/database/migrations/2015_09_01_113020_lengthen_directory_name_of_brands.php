<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class LengthenDirectoryNameOfBrands extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('brands', function(Blueprint $table) {
			DB::statement("ALTER TABLE `brands` MODIFY COLUMN `directory_name` VARCHAR(255) NOT NULL DEFAULT ''");
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('brands', function(Blueprint $table) {
			DB::statement("ALTER TABLE `brands` MODIFY COLUMN `directory_name` VARCHAR(20) NOT NULL DEFAULT ''");
		});
	}

}
