<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTableExternalTwStreams extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('external_tw_streams', function (Blueprint $table) {
            $table->increments('id');

            $table->string('social_media_account_id');
            $table->string('name');
            $table->string('screen_name');
            $table->longText('extra_data');
            $table->string('url');
            $table->longText('token');
            $table->string('token_secret');
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();

            $table->index('social_media_account_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('external_tw_streams');
    }
}

?>