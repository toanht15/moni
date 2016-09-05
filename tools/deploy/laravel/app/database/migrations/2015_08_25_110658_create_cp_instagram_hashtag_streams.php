<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCpInstagramHashtagStreams extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('cp_instagram_hashtag_streams', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('brand_id');
            $table->tinyInteger('display_panel_limit')->default(0);
            $table->boolean('panel_hidden_flg')->default(1);
            $table->boolean('del_flg')->default(0);
            $table->timestamps();

            $table->foreign('brand_id')->references('id')->on('brands');
        });

        DB::statement("INSERT INTO cp_instagram_hashtag_streams(brand_id) SELECT id FROM brands WHERE del_flg = 0;");

        Schema::create('cp_instagram_hashtag_entries', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('stream_id');
            $table->unsignedInteger('instagram_hashtag_user_post_id');
            $table->boolean('priority_flg')->default(0);
            $table->boolean('hidden_flg')->default(0);
            $table->tinyInteger('display_type')->default(1);
            $table->dateTime('pub_date')->default('0000-00-00 00:00:00');
            $table->boolean('del_flg')->default(0);
            $table->timestamps();

            $table->foreign('stream_id')->references('id')->on('cp_instagram_hashtag_streams');
            $table->foreign('instagram_hashtag_user_post_id', 'insta_post_id')->references('id')->on('instagram_hashtag_user_posts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('cp_instagram_hashtag_entries');
        Schema::drop('cp_instagram_hashtag_streams');
    }

}
