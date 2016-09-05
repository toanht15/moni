<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertFbUpdatePageTask extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::table('crawler_types')->insert(
			array(
				'name' => 'facebook_update_page_info',
				'crawler_id' => '0',
				'database_id' => '0',
				'last_crawled_date' => date('Y-m-d H:i:s'),
				'crawl_interval' => '86400',
				'fetch_interval' => '-1',
				'timeout' => '0',
				'stream_type' => '1',
				'fetcher_type' => '1',
				'task_name' => 'FacebookUpdatePageInfoTask',
				'process_urls' => '0'
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
		DB::table('crawler_types')->where('name', 'facebook_update_page_info')->delete();
	}

}
