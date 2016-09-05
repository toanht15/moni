<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableBrandsUsersRelations extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('brands_users_relations', function(Blueprint $table)
		{
			$table->boolean('withdraw_flg')->default(false)->after('referrer');
			$table->boolean('del_info_flg')->default(false)->after('withdraw_flg');
			$table->index('withdraw_flg');
			$table->index('del_info_flg');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('brands_users_relations', function(Blueprint $table)
		{
			$table->dropIndex('brands_users_relations_withdraw_flg_index');
			$table->dropIndex('brands_users_relations_del_info_flg_index');
			$table->dropColumn('withdraw_flg');
			$table->dropColumn('del_info_flg');
		});
	}

}
