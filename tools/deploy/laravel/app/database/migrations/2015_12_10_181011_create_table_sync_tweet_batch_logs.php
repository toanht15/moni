<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableSyncTweetBatchLogs extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('sync_tweet_batch_logs', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('batch_date', 255)->default('0000-00-00');
            $table->unsignedInteger('last_tweet_msg_id')->default(0);
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();

            $table->index('batch_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('sync_tweet_batch_logs');
    }

}
