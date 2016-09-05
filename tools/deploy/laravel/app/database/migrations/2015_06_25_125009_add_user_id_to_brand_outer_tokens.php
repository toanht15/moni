<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserIdToBrandOuterTokens extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('brand_outer_tokens', function(Blueprint $table)
        {
            $table->bigInteger('user_id')->unsigned()->after('social_app_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('brand_outer_tokens', function(Blueprint $table)
        {
            $table->dropColumn('user_id');
        });
    }
}
