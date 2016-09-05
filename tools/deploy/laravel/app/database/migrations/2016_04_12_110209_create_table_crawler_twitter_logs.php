<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTableCrawlerTwitterLogs extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('crawler_twitter_logs', function (Blueprint $t) {
            $t->bigIncrements('id');
            $t->string('batch_date', 255)->default('0000-00-00');
            $t->bigInteger('last_id');
            $t->tinyInteger('type');
            $t->tinyInteger('crawler_type');
            $t->tinyInteger('del_flg')->default(0);
            $t->timestamps();

            $t->index(array('last_id'));
            $t->unique(array('batch_date','crawler_type', 'type'), 'crawler_twitter_logs_unique_key');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('crawler_twitter_logs');
    }

}
