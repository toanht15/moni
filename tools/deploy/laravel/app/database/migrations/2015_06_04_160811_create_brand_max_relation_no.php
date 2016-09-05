<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBrandMaxRelationNo extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('brand_max_relation_no', function(Blueprint $table)
        {
            $table->increments('id');
            $table->unsignedInteger('brand_id');
            $table->bigInteger('max_no')->default(0);;
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
        Schema::drop('brand_max_relation_no');
    }

}
