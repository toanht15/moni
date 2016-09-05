<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropFileTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::drop('file_image');
        Schema::drop('file');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::create('file', function($t) {
            // auto increment id (primary key)
            $t->bigIncrements('id');

            $t->bigInteger('file_no');
            $t->string('file_name', 20)->default('');
            $t->integer('file_type')->default(0);
            $t->string('extension', 10)->default('');
            $t->integer('size')->default(0);
            $t->string('object_id', 50)->default('');
            $t->string('unit', 50)->default('');
            $t->string('url');
            $t->tinyInteger('del_flg')->default(0);
            // created_at, updated_at DATETIME
            $t->dateTime('date_created')->default('0000-00-00 00:00:00');
            $t->dateTime('date_updated')->default('0000-00-00 00:00:00');
            $t->index('object_id');
            $t->index('unit');
        });

        Schema::create('file_image', function($t) {
            // auto increment id (primary key)
            $t->bigIncrements('id');

            $t->bigInteger('file_id')->unsigned();
            $t->string('image_type', 10)->default('');
            $t->integer('width')->default(100);
            $t->integer('height')->default(100);
            $t->tinyInteger('del_flg')->default(0);
            // created_at, updated_at DATETIME
            $t->dateTime('date_created')->default('0000-00-00 00:00:00');
            $t->dateTime('date_updated')->default('0000-00-00 00:00:00');
            $t->index('file_id');
            $t->foreign('file_id')->references('id')->on('file');
        });
	}

}
