<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateGrowthUserStats extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('growth_user_stats', function (Blueprint $table) {

            $table->bigInteger('user_id')->unsigned();
            $table->integer('status')->unsigned()->default(0);
            $table->integer('activated_by_cp_id')->unsigned();
            $table->dateTime('activated_at');
            $table->dateTime('last_activated_at');
            $table->timestamps();

            $table->primary('user_id');
            $table->index('activated_by_cp_id');
            $table->index('activated_at');
            $table->index('last_activated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('growth_user_stats');
    }

}
