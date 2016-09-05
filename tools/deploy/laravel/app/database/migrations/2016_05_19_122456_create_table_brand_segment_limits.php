<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableBrandSegmentLimits extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('brand_segment_limits', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('brand_id');
            $table->tinyInteger('segment_type');
            $table->integer('segment_limit');
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();

            $table->foreign('brand_id')->references('id')->on('brands');
            $table->unique(array('brand_id', 'segment_type'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('brand_segment_limits');
    }

}
