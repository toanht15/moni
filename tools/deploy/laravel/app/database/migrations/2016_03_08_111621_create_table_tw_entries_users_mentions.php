<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTableTwEntriesUsersMentions extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('tw_entries_users_mentions', function (Blueprint $table) {
            $table->increments('id');

            $table->string('tw_uid');
            $table->bigInteger('object_id');
            $table->string('mentioned_uid');
            $table->text('text');
            $table->tinyInteger('del_flg')->default(0);
            // created_at, updated_at DATETIME
            $table->timestamps();

            $table->index('tw_uid');
            $table->index('object_id');
            $table->index('mentioned_uid');
            $table->unique('object_id', 'tw_entries_users_mentions_unique_key');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('tw_entries_users_mentions');
    }

}
