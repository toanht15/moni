<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAatrackerSiteIdToBrands extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('brands', function(Blueprint $table)
        {
            $table->integer('aatracker_site_id')->after('enterprise_id')->default(0);
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('brands', function(Blueprint $table)
        {
            $table->dropColumn('aatracker_site_id');
        });
	}

}
