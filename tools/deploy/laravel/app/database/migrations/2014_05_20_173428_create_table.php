<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
    {
        Schema::create('brands', function ($t) {
            // auto increment id (primary key)
            $t->increments('id');

            $t->bigInteger('enterprise_id')->unsigned();
            $t->string('name', 40)->default('');
            $t->string('profile_img_url')->default('');
            $t->string('background_img_url')->default('');
            $t->string('side_menu_title')->default('');
            $t->tinyInteger('side_menu_title_type')->default(0);
            $t->string('directory_name', 20)->default('');
            $t->string('color_main', 7)->default('');
            $t->string('color_background', 7)->default('');
            $t->string('color_text', 7)->default('');
            $t->tinyInteger('background_img_x')->default(0);
            $t->tinyInteger('background_img_y')->default(0);
            $t->tinyInteger('del_flg')->default(0);
            // created_at, updated_at DATETIME
            $t->timestamps();
            $t->unique('enterprise_id');
        });

        Schema::create('brand_global_menus', function ($t) {
            // auto increment id (primary key)
            $t->increments('id');

            $t->integer('brand_id')->unsigned();
            $t->integer('list_order')->default(0);
            $t->string('name', 40)->default('');
            $t->string('link')->default('');
            $t->tinyInteger('is_blank_flg')->default(0);
            $t->tinyInteger('hidden_flg')->default(0);
            $t->tinyInteger('del_flg')->default(0);
            // created_at, updated_at DATETIME
            $t->timestamps();
            $t->index('brand_id');
            $t->foreign('brand_id')->references('id')->on('brands');
        });

        Schema::create('brand_side_menus', function ($t) {
            // auto increment id (primary key)
            $t->increments('id');

            $t->integer('brand_id')->unsigned();
            $t->integer('list_order')->default(0);
            $t->string('name', 40)->default('');
            $t->string('image_url')->default('');
            $t->string('link')->default('');
            $t->tinyInteger('is_blank_flg')->default(0);
            $t->tinyInteger('hidden_flg')->default(0);
            $t->tinyInteger('del_flg')->default(0);
            // created_at, updated_at DATETIME
            $t->timestamps();
            $t->index('brand_id');
            $t->foreign('brand_id')->references('id')->on('brands');
        });

        Schema::create('users', function ($t) {
            // auto increment id (primary key)
            $t->bigIncrements('id');

            $t->bigInteger('monipla_user_id');
            $t->string('name', 40)->default('');
            $t->string('profile_image_url')->default('');
            $t->string('mp_access_token')->default('');
            $t->string('mp_refresh_token')->default('');
            $t->dateTime('mp_token_update_at')->default('0000-00-00 00:00:00');
            $t->tinyInteger('del_flg')->default(0);
            // created_at, updated_at DATETIME
            $t->timestamps();
            $t->unique('monipla_user_id');
        });

        Schema::create('brand_social_accounts', function ($t) {
            // auto increment id (primary key)
            $t->increments('id');

            $t->integer('brand_id')->unsigned();
            $t->string('social_media_account_id');
            $t->integer('social_app_id');
            $t->longText('token');
            $t->string('token_secret')->default('');
            $t->dateTime('token_update_at')->default('0000-00-00 00:00:00');
            $t->string('name', 255)->default('');
            $t->string('screen_name')->default('');
            $t->string('picture_url')->default('');
            $t->text('store');
            $t->tinyInteger('del_flg')->default(0);
            // created_at, updated_at DATETIME
            $t->timestamps();
            $t->unique(array('social_media_account_id', 'social_app_id'), 'brand_social_accounts_unique_key');
            $t->index('social_app_id');
            $t->index('brand_id');
            $t->foreign('brand_id')->references('id')->on('brands');
        });

        Schema::create('brands_users_relations', function ($t) {
            // auto increment id (primary key)
            $t->bigIncrements('id');

            $t->integer('brand_id')->unsigned();
            $t->bigInteger('user_id')->unsigned();
            $t->bigInteger('no');
            $t->tinyInteger('admin_flg')->default(0);
            $t->tinyInteger('from_kind')->default(0);
            $t->tinyInteger('del_flg')->default(0);
            // created_at, updated_at DATETIME
            $t->timestamps();
            $t->unique(array('brand_id', 'user_id'));
            $t->index('brand_id');
            $t->index('user_id');
            $t->foreign('brand_id')->references('id')->on('brands');
            $t->foreign('user_id')->references('id')->on('users');
        });

        Schema::create('crawler_hosts', function ($t) {
            // auto increment id (primary key)
            $t->increments('id');

            $t->string('name');
            $t->string('robots_file_last_modified', 32);
            $t->string('robots_file_file_size', 20);
            $t->longText('robots_file_body');
            $t->dateTime('robots_file_fetched_date')->nullable();
            $t->dateTime('robots_file_next_fetch_date')->nullable();
            $t->tinyInteger('robots_file_result');
            $t->tinyInteger('ignore_robots_file');
            $t->tinyInteger('del_flg')->default(0);
            // created_at, updated_at DATETIME
            $t->timestamps();
            $t->unique('name');
        });

        Schema::create('crawler_types', function ($t) {
            // auto increment id (primary key)
            $t->increments('id');

            $t->string('name');
            $t->integer('crawler_id');
            $t->integer('database_id');
            $t->dateTime('last_crawled_date')->nullable();
            $t->bigInteger('crawl_interval');
            $t->bigInteger('fetch_interval');
            $t->integer('timeout');
            $t->integer('stream_type');
            $t->integer('fetcher_type');
            $t->string('task_name');
            $t->integer('process_urls');
            $t->tinyInteger('stop_flg')->default(0);
            $t->tinyInteger('del_flg')->default(0);
            // created_at, updated_at DATETIME
            $t->timestamps();
            $t->unique('name');
        });

        Schema::create('crawler_urls', function ($t) {
            // auto increment id (primary key)
            $t->increments('id');

            $t->integer('crawler_type_id')->unsigned();
            $t->integer('crawler_host_id')->unsigned();
            $t->string('target_id');
            $t->string('content_type');
            $t->string('last_modified', 32);
            $t->string('etag');
            $t->string('file_size', 20);
            $t->string('url', 735);
            $t->string('title');
            $t->longText('content');
            $t->dateTime('last_crawled_date');
            $t->dateTime('next_crawled_date');
            $t->bigInteger('crawl_interval');
            $t->tinyInteger('result');
            $t->integer('time_out');
            $t->integer('status_code');
            $t->tinyInteger('errors_count')->default(0);
            $t->string('errors_message', 512)->default('');
            $t->tinyInteger('stop_flg')->default(0);
            $t->tinyInteger('hidden_flg')->default(0);
            $t->tinyInteger('del_flg')->default(0);
            // created_at, updated_at DATETIME
            $t->timestamps();
            $t->unique('target_id');
            $t->index('crawler_type_id');
            $t->index('crawler_host_id');
            $t->foreign('crawler_host_id')->references('id')->on('crawler_hosts');
            $t->foreign('crawler_type_id')->references('id')->on('crawler_types');
        });

        Schema::create('facebook_streams', function ($t) {
            // auto increment id (primary key)
            $t->increments('id');

            $t->integer('brand_id')->unsigned();
            $t->integer('brand_social_account_id')->unsigned();
            $t->tinyInteger('kind')->default(0);
            $t->tinyInteger('entry_detail_update_flg')->default(0);
            $t->tinyInteger('hidden_flg')->default(0);
            $t->tinyInteger('entry_hidden_flg')->default(0);
            $t->tinyInteger('del_flg')->default(0);
            // created_at, updated_at DATETIME
            $t->timestamps();
            $t->unique(array('brand_id', 'brand_social_account_id', 'kind'), 'facebook_streams_unique');
            $t->index('brand_social_account_id');
            $t->foreign('brand_id')->references('id')->on('brands');
            $t->foreign('brand_social_account_id')->references('id')->on('brand_social_accounts');
        });

        Schema::create('facebook_entries', function ($t) {
            // auto increment id (primary key)
            $t->increments('id');

            $t->integer('stream_id')->unsigned();
            $t->string('post_id')->default('');
            $t->bigInteger('object_id');
            $t->string('type')->default('');
            $t->string('link');
            $t->string('image_url')->default('');
            $t->string('creator_id', 512);
            $t->longText('extra_data');
            $t->longText('detail_data');
            $t->dateTime('pub_date');
            $t->dateTime('update_date');
            $t->tinyInteger('detail_data_update_flg')->default(0);
            $t->tinyInteger('detail_data_update_error_count')->default(0);
            $t->tinyInteger('hidden_flg')->default(0);
            $t->tinyInteger('priority_flg')->default(0);
            $t->tinyInteger('display_type')->default(1);
            $t->string('panel_text')->default('');
            $t->tinyInteger('del_flg')->default(0);
            // created_at, updated_at DATETIME
            $t->timestamps();
            $t->index('object_id');
            $t->index('stream_id');
            $t->foreign('stream_id')->references('id')->on('facebook_streams');
        });

        Schema::create('file', function ($t) {
            // auto increment id (primary key)
            $t->bigIncrements('id');

            $t->bigInteger('file_no');
            $t->string('file_name', 20)->default('');
            $t->integer('file_type')->default(0);
            $t->string('extension', 10)->default('');
            $t->integer('size')->default(0);
            $t->string('object_id', 50)->default('');
            $t->string('unit', 50)->default('');
            $t->string('url');
            $t->tinyInteger('del_flg')->default(0);
            // created_at, updated_at DATETIME
            $t->dateTime('date_created')->default('0000-00-00 00:00:00');
            $t->dateTime('date_updated')->default('0000-00-00 00:00:00');
            $t->index('object_id');
            $t->index('unit');
        });

        Schema::create('file_image', function ($t) {
            // auto increment id (primary key)
            $t->bigIncrements('id');

            $t->bigInteger('file_id')->unsigned();
            $t->string('image_type', 10)->default('');
            $t->integer('width')->default(100);
            $t->integer('height')->default(100);
            $t->tinyInteger('del_flg')->default(0);
            // created_at, updated_at DATETIME
            $t->dateTime('date_created')->default('0000-00-00 00:00:00');
            $t->dateTime('date_updated')->default('0000-00-00 00:00:00');
            $t->index('file_id');
            $t->foreign('file_id')->references('id')->on('file');
        });

        Schema::create('free_area_entries', function ($t) {
            // auto increment id (primary key)
            $t->increments('id');

            $t->integer('brand_id')->unsigned();
            $t->longText('body');
            $t->tinyInteger('public_flg')->default(0);
            $t->tinyInteger('del_flg')->default(0);
            // created_at, updated_at DATETIME
            $t->timestamps();
            $t->index('brand_id');
            $t->foreign('brand_id')->references('id')->on('brands');
        });

        Schema::create('link_entries', function ($t) {
            // auto increment id (primary key)
            $t->increments('id');

            $t->integer('brand_id')->unsigned();
            $t->string('title')->default('');
            $t->longText('body');
            $t->string('link')->default('');
            $t->string('image_url')->default('');
            $t->dateTime('pub_date')->default('0000-00-00 00:00:00');
            $t->tinyInteger('priority_flg')->default(0);
            $t->tinyInteger('display_type')->default(1);
            $t->tinyInteger('hidden_flg')->default(0);
            $t->tinyInteger('del_flg')->default(0);
            // created_at, updated_at DATETIME
            $t->timestamps();
            $t->index('brand_id');
            $t->foreign('brand_id')->references('id')->on('brands');
        });

        Schema::create('log4php_log', function ($t) {
            // auto increment id (primary key)
            $t->bigIncrements('id');

            $t->dateTime('timestamp')->default('0000-00-00 00:00:00');
            $t->string('logger')->default('');
            $t->string('level')->default('');
            $t->string('message', 4000)->default('');
            $t->integer('thread');
            $t->string('file')->default('');
            $t->string('line', 10)->default('');
            $t->string('request', 1000)->default('');
            $t->string('cookie', 1000)->default('');
        });

        Schema::create('rss_streams', function ($t) {
            // auto increment id (primary key)
            $t->increments('id');

            $t->integer('brand_id')->unsigned();
            $t->string('link')->default('');
            $t->string('rss_url')->default('');
            $t->string('image_url')->default('');
            $t->string('title')->default('');
            $t->string('description')->default('');
            $t->string('language', 25)->default('');
            $t->tinyInteger('entry_detail_update_flg')->default(0);
            $t->tinyInteger('hidden_flg')->default(0);
            $t->tinyInteger('entry_hidden_flg')->default(0);
            $t->tinyInteger('del_flg')->default(0);
            // created_at, updated_at DATETIME
            $t->timestamps();
            $t->index('brand_id');
            $t->foreign('brand_id')->references('id')->on('brands');
        });

        Schema::create('rss_entries', function ($t) {
            // auto increment id (primary key)
            $t->increments('id');

            $t->integer('stream_id')->unsigned();
            $t->string('author')->default('');
            $t->string('guid')->default('');
            $t->string('language')->default('');
            $t->longText('text');
            $t->string('link')->default('');
            $t->string('image_url')->default('');
            $t->longText('description');
            $t->dateTime('pub_date')->default('0000-00-00 00:00:00');
            $t->tinyInteger('hidden_flg')->default(0);
            $t->tinyInteger('priority_flg')->default(0);
            $t->tinyInteger('display_type')->default(1);
            $t->string('panel_text')->default('');
            $t->tinyInteger('del_flg')->default(0);
            // created_at, updated_at DATETIME
            $t->timestamps();
            $t->index('stream_id');
            $t->foreign('stream_id')->references('id')->on('rss_streams');
        });

        Schema::create('social_apps', function ($t) {
            // auto increment id (primary key)
            $t->increments('id');

            $t->tinyInteger('provider');
            $t->string('name', 40)->default('');
            $t->tinyInteger('del_flg')->default(0);
            // created_at, updated_at DATETIME
            $t->timestamps();
        });

        Schema::create('static_html_entries', function ($t) {
            // auto increment id (primary key)
            $t->increments('id');

            $t->integer('brand_id')->unsigned();
            $t->string('page_url')->default('');
            $t->string('title')->default('');
            $t->longText('body');
            $t->tinyInteger('hidden_flg')->default(0);
            $t->tinyInteger('priority_flg')->default(0);
            $t->tinyInteger('del_flg')->default(0);
            // created_at, updated_at DATETIME
            $t->timestamps();
            $t->index('brand_id');
            $t->foreign('brand_id')->references('id')->on('brands');
        });

        Schema::create('twitter_streams', function ($t) {
            // auto increment id (primary key)
            $t->increments('id');

            $t->integer('brand_id')->unsigned();
            $t->integer('brand_social_account_id')->unsigned();
            $t->tinyInteger('kind')->default(0);
            $t->tinyInteger('hidden_flg')->default(1);
            $t->tinyInteger('entry_hidden_flg')->default(1);
            $t->tinyInteger('del_flg')->default(0);
            // created_at, updated_at DATETIME
            $t->timestamps();
            $t->index('brand_social_account_id');
            $t->foreign('brand_id')->references('id')->on('brands');
            $t->foreign('brand_social_account_id')->references('id')->on('brand_social_accounts');
        });

        Schema::create('twitter_entries', function ($t) {
            // auto increment id (primary key)
            $t->increments('id');

            $t->integer('stream_id')->unsigned();
            $t->bigInteger('object_id');
            $t->string('link');
            $t->string('image_url')->default('');
            $t->string('creator_id');
            $t->longText('extra_data');
            $t->dateTime('pub_date');
            $t->dateTime('update_date');
            $t->tinyInteger('hidden_flg')->default(0);
            $t->tinyInteger('priority_flg')->default(0);
            $t->tinyInteger('display_type')->default(1);
            $t->string('panel_text')->default('');
            $t->tinyInteger('del_flg')->default(0);
            // created_at, updated_at DATETIME
            $t->timestamps();
            $t->unique(array('object_id', 'stream_id'));
            $t->index('object_id');
            $t->index('stream_id');
            $t->foreign('stream_id')->references('id')->on('twitter_streams');
        });

        Schema::create('youtube_streams', function ($t) {
            // auto increment id (primary key)
            $t->increments('id');

            $t->integer('brand_id')->unsigned();
            $t->integer('brand_social_account_id')->unsigned();
            $t->tinyInteger('kind')->default(0);
            $t->tinyInteger('entry_detail_update_flg')->default(0);
            $t->tinyInteger('hidden_flg')->default(0);
            $t->tinyInteger('entry_hidden_flg')->default(0);
            $t->tinyInteger('del_flg')->default(0);
            // created_at, updated_at DATETIME
            $t->timestamps();
            $t->unique(array('brand_id', 'brand_social_account_id', 'kind'));
            $t->index('brand_social_account_id');
            $t->foreign('brand_id')->references('id')->on('brands');
            $t->foreign('brand_social_account_id')->references('id')->on('brand_social_accounts');
        });

        Schema::create('youtube_entries', function ($t) {
            // auto increment id (primary key)
            $t->increments('id');

            $t->integer('stream_id')->unsigned();
            $t->string('object_id')->default('');
            $t->string('link');
            $t->string('image_url')->default('');
            $t->longText('detail_data');
            $t->dateTime('pub_date');
            $t->tinyInteger('hidden_flg')->default(0);
            $t->tinyInteger('priority_flg')->default(0);
            $t->tinyInteger('display_type')->default(1);
            $t->string('panel_text')->default('');
            $t->tinyInteger('del_flg')->default(0);
            // created_at, updated_at DATETIME
            $t->timestamps();
            $t->unique(array('object_id', 'stream_id'));
            $t->index('object_id');
            $t->index('stream_id');
            $t->foreign('stream_id')->references('id')->on('youtube_streams');
        });

        //init database
        $value = array(
            array(
                'name' => 'dummy',
                'crawler_id' => 0,
                'database_id' => 0,
                'last_crawled_date' => '2013-09-25 12:00:10',
                'crawl_interval' => 86400,
                'fetch_interval' => -1,
                'timeout' => 0,
                'stream_type' => 1,
                'fetcher_type' => 1,
                'task_name' => 'DummyTask',
                'process_urls' => 0,
                'stop_flg' => 0,
                'del_flg' => 0,
                'created_at' => '2013-04-23 01:49:18',
                'updated_at' => '2013-04-23 01:49:18'
            ),
            array(
                'name' => 'twitter_user_timeline_user_auth',
                'crawler_id' => 0,
                'database_id' => 0,
                'last_crawled_date' => '2013-09-25 12:00:10',
                'crawl_interval' => 86400,
                'fetch_interval' => -1,
                'timeout' => 0,
                'stream_type' => 1,
                'fetcher_type' => 1,
                'task_name' => 'TwitterUserTimelineUserAuthTask',
                'process_urls' => 0,
                'stop_flg' => 0,
                'del_flg' => 0,
                'created_at' => '2013-04-23 01:49:18',
                'updated_at' => '2013-04-23 01:49:18'
            ),
            array(
                'name' => 'facebook_user_post_user_auth',
                'crawler_id' => 0,
                'database_id' => 0,
                'last_crawled_date' => '2013-09-25 12:00:10',
                'crawl_interval' => 86400,
                'fetch_interval' => -1,
                'timeout' => 0,
                'stream_type' => 1,
                'fetcher_type' => 1,
                'task_name' => 'FacebookUserPostUserAuthTask',
                'process_urls' => 0,
                'stop_flg' => 0,
                'del_flg' => 0,
                'created_at' => '2013-04-23 01:49:18',
                'updated_at' => '2013-04-23 01:49:18'
            ),
            array(
                'name' => 'facebook_update_detail_user_auth',
                'crawler_id' => 0,
                'database_id' => 0,
                'last_crawled_date' => '2013-09-25 12:00:10',
                'crawl_interval' => 86400,
                'fetch_interval' => -1,
                'timeout' => 0,
                'stream_type' => 1,
                'fetcher_type' => 1,
                'task_name' => 'FacebookGetPostDetailUserAuthTask',
                'process_urls' => 0,
                'stop_flg' => 0,
                'del_flg' => 0,
                'created_at' => '2013-04-23 01:49:18',
                'updated_at' => '2013-04-23 01:49:18'
            ),
            array(
                'name' => 'facebook_get_posts_detail_user_auth',
                'crawler_id' => 0,
                'database_id' => 0,
                'last_crawled_date' => '2013-09-25 12:00:10',
                'crawl_interval' => 86400,
                'fetch_interval' => -1,
                'timeout' => 0,
                'stream_type' => 1,
                'fetcher_type' => 1,
                'task_name' => 'FacebookGetPostsDetailUserAuthTask',
                'process_urls' => 0,
                'stop_flg' => 0,
                'del_flg' => 0,
                'created_at' => '2013-04-23 01:49:18',
                'updated_at' => '2013-04-23 01:49:18'
            ),
            array(
                'name' => 'youtube_user_post_user_auth',
                'crawler_id' => 0,
                'database_id' => 0,
                'last_crawled_date' => '2013-09-25 12:00:10',
                'crawl_interval' => 86400,
                'fetch_interval' => -1,
                'timeout' => 0,
                'stream_type' => 1,
                'fetcher_type' => 1,
                'task_name' => 'YoutubeUserPostUserAuthTask',
                'process_urls' => 0,
                'stop_flg' => 0,
                'del_flg' => 0,
                'created_at' => '2013-04-23 01:49:18',
                'updated_at' => '2013-04-23 01:49:18'
            ),
            array(
                'name' => 'rss_fetch',
                'crawler_id' => 0,
                'database_id' => 0,
                'last_crawled_date' => '2013-09-25 12:00:10',
                'crawl_interval' => 86400,
                'fetch_interval' => -1,
                'timeout' => 0,
                'stream_type' => 1,
                'fetcher_type' => 1,
                'task_name' => 'RssFetchTask',
                'process_urls' => 0,
                'stop_flg' => 0,
                'del_flg' => 0,
                'created_at' => '2013-04-23 01:49:18',
                'updated_at' => '2013-04-23 01:49:18'
            )
        );
        DB::table('crawler_types')->insert($value);

        $value = array(
            array(
                'name' => 'twitter',
                'robots_file_last_modified' => '',
                'robots_file_file_size' => '',
                'robots_file_body' => '',
                'robots_file_fetched_date' => null,
                'robots_file_next_fetch_date' => null,
                'robots_file_result' => 1,
                'ignore_robots_file' => 1,
                'created_at' => '2013-04-23 01:27:28',
                'updated_at' => '2013-04-23 01:27:28'
            ),
            array(
                'name' => 'facebook',
                'robots_file_last_modified' => '',
                'robots_file_file_size' => '',
                'robots_file_body' => '',
                'robots_file_fetched_date' => null,
                'robots_file_next_fetch_date' => null,
                'robots_file_result' => 1,
                'ignore_robots_file' => 1,
                'created_at' => '2013-04-23 01:27:28',
                'updated_at' => '2013-04-23 01:27:28'
            ),
            array(
                'name' => 'api.monipla.jp',
                'robots_file_last_modified' => '',
                'robots_file_file_size' => '',
                'robots_file_body' => '',
                'robots_file_fetched_date' => null,
                'robots_file_next_fetch_date' => null,
                'robots_file_result' => 1,
                'ignore_robots_file' => 1,
                'created_at' => '2013-04-23 01:27:28',
                'updated_at' => '2013-04-23 01:27:28'
            ),
            array(
                'name' => 'google',
                'robots_file_last_modified' => '',
                'robots_file_file_size' => '',
                'robots_file_body' => '',
                'robots_file_fetched_date' => null,
                'robots_file_next_fetch_date' => null,
                'robots_file_result' => 1,
                'ignore_robots_file' => 1,
                'created_at' => '2013-04-23 01:27:28',
                'updated_at' => '2013-04-23 01:27:28'
            ),
            array(
                'name' => 'rss',
                'robots_file_last_modified' => '',
                'robots_file_file_size' => '',
                'robots_file_body' => '',
                'robots_file_fetched_date' => null,
                'robots_file_next_fetch_date' => null,
                'robots_file_result' => 1,
                'ignore_robots_file' => 1,
                'created_at' => '2013-04-23 01:27:28',
                'updated_at' => '2013-04-23 01:27:28'
            )
        );
        DB::table('crawler_hosts')->insert($value);

        $value = array(
            array(
                'provider' => '1',
                'name' => 'twitter',
                'del_flg' => 0,
                'created_at' => '2013-04-23 13:06:44',
                'updated_at' => '2013-04-23 13:06:44'
            ),
            array(
                'provider' => '2',
                'name' => 'facebook',
                'del_flg' => 0,
                'created_at' => '2013-04-23 13:06:44',
                'updated_at' => '2013-04-23 13:06:44'
            ),
            array(
                'provider' => '3',
                'name' => 'google',
                'del_flg' => 0,
                'created_at' => '2013-04-23 13:06:44',
                'updated_at' => '2013-04-23 13:06:44'
            ),
            array(
                'provider' => '4',
                'name' => 'rss',
                'del_flg' => 0,
                'created_at' => '2013-04-23 13:06:44',
                'updated_at' => '2013-04-23 13:06:44'
            )
        );
        DB::table('social_apps')->insert($value);
    }

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::drop('brand_global_menus');
        Schema::drop('brand_side_menus');

        Schema::drop('brands_users_relations');
        Schema::drop('users');
        Schema::drop('crawler_urls');
        Schema::drop('crawler_hosts');
        Schema::drop('crawler_types');
        Schema::drop('facebook_entries');
        Schema::drop('facebook_streams');
        Schema::drop('file_image');
        Schema::drop('file');
        Schema::drop('free_area_entries');
        Schema::drop('link_entries');
        Schema::drop('rss_entries');
        Schema::drop('rss_streams');
        Schema::drop('log4php_log');
        Schema::drop('social_apps');
        Schema::drop('static_html_entries');
        Schema::drop('twitter_entries');
        Schema::drop('twitter_streams');
        Schema::drop('youtube_entries');
        Schema::drop('youtube_streams');
        Schema::drop('brand_social_accounts');
        Schema::drop('brands');

    }

}
