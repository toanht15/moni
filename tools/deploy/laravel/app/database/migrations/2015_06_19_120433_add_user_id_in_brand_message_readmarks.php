<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserIdInBrandMessageReadmarks extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
        Schema::table('brand_message_readmarks', function (Blueprint $t) {
            $t->bigInteger('user_id')->after('brand_id');
        });
    }

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
        Schema::table('brand_message_readmarks', function (Blueprint $t) {
            $t->dropColumn('user_id');
        });
    }

}
