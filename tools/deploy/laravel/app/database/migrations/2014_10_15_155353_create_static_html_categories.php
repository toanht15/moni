<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStaticHtmlCategories extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('static_html_categories', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('brand_id')->unsigned();
            $table->string('name',255)->default('');
            $table->string('directory',255)->default('');
            $table->text('description');
            $table->text('keyword');
            $table->string('og_image_url',255)->default('');
            $table->integer('depth')->default(0);
            $table->integer('order_no')->default(0);
            $table->tinyInteger('is_use_customize')->default(0);
            $table->longText('customize_code');
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();
            $table->index('name');
            $table->index('id');
            $table->foreign('brand_id')->references('id')->on('brands');
            $table->unique(array('brand_id', 'name'));
        });

        Schema::create('static_html_category_relations', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('parent_id')->unsigned();
            $table->integer('children_id')->unsigned();
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();
            $table->index('parent_id');
            $table->index('children_id');
            $table->foreign('parent_id')->references('id')->on('static_html_categories');
            $table->foreign('children_id')->references('id')->on('static_html_categories');
            $table->unique(array('parent_id', 'children_id'));
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::drop('static_html_category_relations');
        Schema::drop('static_html_categories');
	}

}
