<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBrandsUsersSearchInfo extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('brands_users_search_info', function(Blueprint $table)
        {
            $table->increments('id');
            $table->unsignedBigInteger('brands_users_relation_id');
            $table->integer('cp_entry_count')->default(0);
            $table->integer('cp_announce_count')->default(0);
            $table->integer('message_delivered_count')->default(0);
            $table->integer('message_read_count')->default(0);
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();
            $table->foreign('brands_users_relation_id')->references('id')->on('brands_users_relations');
            $table->unique('brands_users_relation_id');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('brands_users_search_info');
    }

}
