<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMailQueuesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('mail_queues', function(Blueprint $t)
		{
            $t->increments('id');
            $t->dateTime('send_schedule')->default('0000-00-00 00:00:00');
            $t->string('charset',20)->default('');
            $t->string('real_charset', 20)->default('');
            $t->string('to_address', 255)->default('');
            $t->string('cc_address', 255)->default('');
            $t->string('bcc_address', 255)->default('');
            $t->string('subject', 255)->default('');
            $t->text('body_plain');
            $t->text('body_html');
            $t->string('from_address', 255)->default('');
            $t->string('envelope', 255)->default('');
            // created_at, updated_at DATETIME
            $t->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::drop('mail_queues');
	}

}
