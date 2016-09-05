<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MoniplaFreeItemData extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('monipla_free_items', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('platform_user_id')->default(0);
            $table->unsignedInteger('brand_id')->default(0);
            $table->unsignedBigInteger('free_item_id')->default(0);
            $table->string('input_value', 255)->default('');
            $table->dateTime('user_free_item_updated')->default('0000-00-00 00:00:00');
            $table->boolean('del_flg')->default(0);
            $table->timestamps();

            $table->foreign('brand_id')->references('id')->on('brands');
            $table->foreign('platform_user_id')->references('monipla_user_id')->on('users');
        });

        Schema::create('monipla_free_item_relations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('monipla_free_item')->default(0);
            $table->unsignedInteger('brandco_free_item')->default(0);
            $table->boolean('del_flg')->default(0);
            $table->timestamps();

            $table->foreign('brandco_free_item')->references('id')->on('profile_questionnaires');
        });

        Schema::create('monipla_free_item_syncs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('relation_id')->default(0);
            $table->unsignedInteger('question_id')->default(0);
            $table->String('answer', 255)->default(0);
            $table->boolean('del_flg')->default(0);
            $table->timestamps();

            $table->foreign('relation_id')->references('id')->on('brands_users_relations');
            $table->foreign('question_id')->references('id')->on('profile_questionnaires');
            $table->unique(array('relation_id','question_id'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('monipla_free_items');
        Schema::drop('monipla_free_item_relations');
        Schema::drop('monipla_free_item_syncs');
    }

}
