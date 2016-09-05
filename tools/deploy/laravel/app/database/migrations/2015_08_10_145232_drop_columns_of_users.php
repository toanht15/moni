<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropColumnsOfUsers extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('users', function(Blueprint $table)
        {
            $table->dropColumn('mp_access_token');
            $table->dropColumn('mp_refresh_token');
            $table->dropColumn('mp_client_id');
            $table->dropColumn('mp_token_update_at');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('users', function(Blueprint $table)
        {
            $table->string('mp_access_token')->default('');
            $table->string('mp_refresh_token')->default('');
            $table->text('mp_client_id');
            $table->dateTime('mp_token_update_at')->default('0000-00-00 00:00:00');
        });
	}

}
