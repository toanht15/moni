<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTableTwEntriesUsersReplies extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('tw_entries_users_replies', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('mention_id');
            $table->bigInteger('object_id');
            $table->bigInteger('entry_object_id');
            $table->tinyInteger('del_flg')->default(0);
            // created_at, updated_at DATETIME
            $table->timestamps();

            $table->index('mention_id');
            $table->index('object_id');
            $table->index('entry_object_id');
            $table->unique('mention_id');
            $table->unique('object_id', 'tw_entries_users_replies_unique_key');
            $table->foreign('mention_id')->references('id')->on('tw_entries_users_mentions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('tw_entries_users_replies');
    }

}
