<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesManager extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_managers', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('brand_id')->unsigned();
            $table->integer('sales_manager_id')->unsigned();
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
        Schema::drop('sales_managers');
    }

}