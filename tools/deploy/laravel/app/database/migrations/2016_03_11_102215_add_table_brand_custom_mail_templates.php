<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTableBrandCustomMailTemplates extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('brand_custom_mail_templates', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('brand_id');
            $table->string('sender_name');
            $table->string('subject');
            $table->text('body_plain');
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();
            $table->foreign('brand_id')->references('id')->on('brands');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('brand_custom_mail_templates');
	}

}
