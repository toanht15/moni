<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableInstagramStreamsAndInstagramEntries extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('instagram_streams', function($t) {
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

        Schema::create('instagram_entries', function($t) {
            // auto increment id (primary key)
            $t->increments('id');

            $t->integer('stream_id')->unsigned();
            $t->bigInteger('object_id');
            $t->string('link');
            $t->string('type');
            $t->string('filter');
            $t->string('image_url')->default('');

            $t->dateTime('pub_date');
            $t->longText('extra_data');
            $t->string('panel_text')->default('');

            $t->tinyInteger('hidden_flg')->default(0);
            $t->tinyInteger('priority_flg')->default(0);
            $t->tinyInteger('display_type')->default(1);

            $t->tinyInteger('del_flg')->default(0);
            // created_at, updated_at DATETIME
            $t->timestamps();

            $t->unique(array('object_id','stream_id'));
            $t->index('object_id');
            $t->index('stream_id');
            $t->foreign('stream_id')->references('id')->on('instagram_streams');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('instagram_streams', function(Blueprint $table) {
            $table->dropForeign('instagram_streams_brand_social_account_id_foreign');
            $table->dropForeign('instagram_streams_brand_id_foreign');
        });
        Schema::table('instagram_entries', function(Blueprint $table) {
            $table->dropForeign('instagram_entries_stream_id_foreign');
        });

        Schema::drop('instagram_streams');
        Schema::drop('instagram_entries');
	}
}
