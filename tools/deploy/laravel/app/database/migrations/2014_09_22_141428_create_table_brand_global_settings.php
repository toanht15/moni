<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableBrandGlobalSettings extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('brand_global_settings', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('brand_id')->unsigned();
            $table->string('name',255)->default('');
            $table->string('content',255)->default('');
            $table->timestamps();
            $table->index('brand_id');
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
        Schema::drop('brand_global_settings');
    }

}
