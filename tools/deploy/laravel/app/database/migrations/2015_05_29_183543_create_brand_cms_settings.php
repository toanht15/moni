<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBrandCmsSettings extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('brand_cms_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('brand_id');
            $table->boolean('category_navi_top_display_flg');
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();
            $table->foreign('brand_id')->references('id')->on('brands');
            $table->unique('brand_id');
        });
        DB::statement("INSERT INTO brand_cms_settings(brand_id) SELECT id FROM brands WHERE del_flg = 0;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('brand_cms_settings', function (Blueprint $table) {
            $table->drop();
        });
    }

}
