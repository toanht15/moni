<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTableExternalFbStreams extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('external_fb_streams', function (Blueprint $table) {
            //auto increment key (primary key)
            $table->increments('id');

            $table->string('social_media_account_id');
            $table->string('name');
            $table->string('screen_name');
            $table->string('url');
            $table->longText('extra_data');
            $table->tinyInteger('del_flg')->default(0);
            // created_at, updated_at DATETIME
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('external_fb_streams');
    }
}

?>