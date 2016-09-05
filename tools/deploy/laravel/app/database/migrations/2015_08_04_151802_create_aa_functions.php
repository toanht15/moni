<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAaFunctions extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('aa_functions', function ($table) {
            // auto increment id (primary key)
            $table->increments('id');

            $table->string('package', 255)->default('');
            $table->string('action', 255)->default('');
            $table->longText('note');

            $table->tinyInteger('del_flg')->default(0);
            // created_at, updated_at DATETIME
            $table->timestamps();

            $table->index(array('package', 'action'));
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::drop('aa_functions');
	}

}
