<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertInstagram extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        DB::table('social_apps')->insert(
            array(
                'provider' => '5',
                'name' => 'instagram',
                'created_at' => new DateTime,
                'updated_at' => new DateTime
            ));
        DB::table('crawler_hosts')->insert(
            array(
                'name' => 'instagram',
                'robots_file_result' => '1',
                'ignore_robots_file' => '1',
                'created_at' => new DateTime,
                'updated_at' => new DateTime
            ));
        DB::table('crawler_types')->insert(
            array(
                'name' => 'instagram_user_recent_media',
                'crawler_id' => '0',
                'database_id' => '0',
                'crawl_interval' => '86400',
                'fetch_interval' => '-1',
                'timeout' => '0',
                'stream_type' => '1',
                'fetcher_type' => '1',
                'task_name' => 'InstagramGetRecentMediaTask',
                'created_at' => new DateTime,
                'updated_at' => new DateTime
            ));
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        DB::table('social_apps')->where('name', 'instagram')->delete();
        DB::table('crawler_hosts')->where('name', 'instagram')->delete();
        DB::table('crawler_types')->where('name', 'instagram_user_recent_media')->delete();
	}

}
