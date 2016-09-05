<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAccountMergeSuggestions extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create(
            'account_merge_suggestions',
            function (Blueprint $table) {
                $table->increments('id');
                $table->bigInteger('to_allied_id')->unsigned();
                $table->bigInteger('from_allied_id')->unsigned();
                $table->tinyInteger('merged')->default(0);
                $table->tinyInteger('del_flg')->default(0);
                $table->timestamps();

                $table->index('to_allied_id');
                $table->index('from_allied_id');
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('account_merge_suggestions');
    }

}
