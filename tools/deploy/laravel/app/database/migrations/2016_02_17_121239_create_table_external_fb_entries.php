<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTableExternalFbEntries extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('external_fb_entries', function (Blueprint $table) {
            //auto increment key (primary key)
            $table->increments('id');

            $table->integer('stream_id')->unsigned();
            $table->string('post_id');
            $table->bigInteger('object_id');
            $table->string('type');
            $table->string('status_type')->default('');
            $table->string('link');
            $table->tinyInteger('del_flg')->default(0);
            // created_at, updated_at DATETIME
            $table->timestamps();
            $table->index('stream_id');
            $table->index('object_id');
            $table->foreign('stream_id')->references('id')->on('external_fb_streams');
            $table->unique('post_id', 'external_fb_entries_unique_key');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('external_fb_entries');
    }
}

?>