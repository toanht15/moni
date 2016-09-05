<?php

use Illuminate\Database\Migrations\Migration;

class DropTableLog4phpLog extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::drop('log4php_log');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::create('log4php_log', function ($table) {
            
            // auto increment id (primary key)
            $table->bigIncrements('id');

            $table->dateTime('timestamp')->default('0000-00-00 00:00:00');
            $table->string('logger')->default('');
            $table->string('level')->default('');
            $table->string('message', 4000)->default('');
            $table->integer('thread');
            $table->string('file')->default('');
            $table->string('line', 10)->default('');
            $table->string('request', 1000)->default('');
            $table->string('cookie', 1000)->default('');
        });
    }

}
