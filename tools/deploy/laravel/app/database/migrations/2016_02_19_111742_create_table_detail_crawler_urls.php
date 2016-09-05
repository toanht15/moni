<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTableDetailCrawlerUrls extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detail_crawler_urls', function (Blueprint $table) {
            //auto increment key (primary key)
            $table->increments('id');

            $table->bigInteger('object_id');
            $table->tinyInteger('type');
            $table->tinyInteger('crawler_type');
            $table->tinyInteger('data_type');
            $table->string('url');
            $table->tinyInteger('del_flg')->default(0);
            // created_at, updated_at DATETIME
            $table->timestamps();
            $table->index('object_id');
            $table->unique(array('object_id','data_type'), 'detail_crawler_urls_unique_key');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('detail_crawler_urls');
    }
}

?>