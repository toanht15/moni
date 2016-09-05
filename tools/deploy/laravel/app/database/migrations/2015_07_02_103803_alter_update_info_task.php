<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUpdateInfoTask extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        DB::table('crawler_types')->where('name', 'facebook_update_page_info')->update(
            array(
                'name' => 'update_social_account_info',
                'task_name' => 'UpdateSocialAccountInfoTask'
            )
        );
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        DB::table('crawler_types')->where('name', 'update_social_account_info')->update(
            array(
                'name' => 'facebook_update_page_info',
                'task_name' => 'FacebookUpdatePageInfoTask'
            )
        );
	}

}
