<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnWinnerLabelForCps extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('cps', function (Blueprint $table) {

            $table->text('recruitment_note')->after('show_lp_flg');
            $table->tinyInteger('use_recruitment_note')->default(2)->after('show_lp_flg');
            $table->string('winner_label', 100)->default('')->after('show_lp_flg');
            $table->tinyInteger('use_winner_label')->default(2)->after('show_lp_flg');

        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('cps', function (Blueprint $table) {

            $table->dropColumn('use_recruitment_note');
            $table->dropColumn('recruitment_note');
            $table->dropColumn('winner_label');
            $table->dropColumn('use_winner_label');

        });
	}

}
