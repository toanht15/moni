<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsForCpMovieActionTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cp_movie_actions', function(Blueprint $table)
        {
            $table->tinyInteger('movie_type')->default(1)->after('movie_object_id');
            $table->string('movie_url', 255)->default("")->after('movie_type');
            $table->tinyInteger('popup_view_flg')->default(0)->after('movie_url');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cp_movie_actions', function(Blueprint $table)
        {
            $table->dropColumn('movie_url');
            $table->dropColumn('movie_type');
            $table->dropColumn('popup_view_flg');
        });
    }

}
