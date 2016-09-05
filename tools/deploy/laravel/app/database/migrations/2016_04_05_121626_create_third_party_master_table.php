<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateThirdPartyMasterTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('third_party_masters', function(Blueprint $table)
        {
            $table->increments('id');
            $table->string('tpk', 255)->default('');
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();

            $table->index('tpk');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('third_party_masters');
    }

}
