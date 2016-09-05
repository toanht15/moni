<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterMoniplaFreeItemsDeleteRelation extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('monipla_free_items', function(Blueprint $table)
        {
            $table->dropForeign('monipla_free_items_platform_user_id_foreign');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('monipla_free_items', function(Blueprint $table)
        {
            $table->foreign('platform_user_id')->references('monipla_user_id')->on('users');
        });
	}

}