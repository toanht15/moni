<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeleteNotuseBatch extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        DB::statement('DELETE FROM crawler_types WHERE name = "facebook_update_detail_user_auth"');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        DB::statement("INSERT INTO `crawler_types` (`id`, `name`, `crawler_id`, `database_id`, `last_crawled_date`, `crawl_interval`, `fetch_interval`, `timeout`, `stream_type`, `fetcher_type`, `task_name`, `process_urls`, `stop_flg`, `del_flg`, `created_at`, `updated_at`)
VALUES
	(4, 'facebook_update_detail_user_auth', 0, 0, '2013-09-25 12:00:10', 86400, -1, 0, 1, 1, 'FacebookGetPostDetailUserAuthTask', 0, 0, 0, '2013-04-23 01:49:18', '2013-04-23 01:49:18');
");
	}

}
