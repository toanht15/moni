<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTableFbEntriesUsersComments extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fb_entries_users_comments', function (Blueprint $table) {
            //auto increment key (primary key)
            $table->increments('id');

            $table->bigInteger('fb_uid')->unsigned();
            $table->bigInteger('post_id')->unsigned();
            $table->text('message');
            $table->tinyInteger('cmt_count');
            $table->integer('cmt_like_count');
            $table->integer('cmt_reply_count');
            $table->tinyInteger('del_flg')->default(0);
            // created_at, updated_at DATETIME
            $table->timestamps();
            $table->index('fb_uid');
            $table->index('post_id');
            $table->unique(array('fb_uid', 'post_id'), 'fb_entries_users_comments_unique_key');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('fb_entries_users_comments');
    }
}

?>