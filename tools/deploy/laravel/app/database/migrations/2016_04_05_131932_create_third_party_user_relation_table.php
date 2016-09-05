<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateThirdPartyUserRelationTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('third_party_user_relations', function(Blueprint $table)
        {
            $table->increments('id');
            $table->bigInteger('user_id')->unsigned();
            $table->integer('third_party_master_id')->unsigned();
            $table->string('value', 255)->default('');
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('third_party_master_id')->references('id')->on('third_party_masters');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('third_party_user_relations');
    }

}
