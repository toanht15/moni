<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFromIdBrandUserRelation extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('brands_users_relations', function(Blueprint $t)
        {
            $t->string('from_id', 255)->nullable()->after('from_kind');
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
            $t->dropColumn('from_id');
        });
	}

}
