<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableBrandPageSettings extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('brand_page_settings', function(Blueprint $t)
        {
            $t->increments('id');
            $t->integer('brand_id')->unsigned();
            $t->tinyInteger('public_flg')->default(2);
            $t->longText('tag_text');
            $t->tinyInteger('del_flg')->default(0);
            // created_at, updated_at DATETIME
            $t->timestamps();
            $t->index('brand_id');
            $t->foreign('brand_id')->references('id')->on('brands');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('brand_page_settings');
    }

}
