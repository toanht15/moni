<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableCpLostMails extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cp_lost_mails', function(Blueprint $table){
            $table->increments('id');
            $table->unsignedBigInteger('user_mail_id');
            $table->unsignedInteger('cp_id');
            $table->timestamps();

            $table->index('user_mail_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('cp_lost_mails');
    }

}