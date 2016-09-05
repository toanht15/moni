<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddReferrerBrandUserRelation extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('brands_users_relations', function(Blueprint $t)
        {
            $t->string('referrer', 255)->nullable()->after('login_count');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('brands_users_relations', function(Blueprint $t)
        {
            $t->dropColumn('referrer');
        });
	}

}
