<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBrandOuterTokensTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('brand_outer_tokens', function(Blueprint $table)
        {
            $table->increments('id');
            $table->unsignedInteger('brand_id');
            $table->unsignedInteger('social_app_id');
            $table->string('token', 255)->default('');;
            $table->string('password', 20)->default('');;
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();
            $table->foreign('brand_id')->references('id')->on('brands');
            $table->foreign('social_app_id')->references('id')->on('social_apps');
            $table->unique(array('token', 'password'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('brand_outer_tokens');
    }
}
