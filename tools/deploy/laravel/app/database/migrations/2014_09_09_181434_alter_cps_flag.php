<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCpsFlag extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('cps', function(Blueprint $table)
        {
            $table->renameColumn('use_winner_label', 'show_winner_label');
            $table->renameColumn('use_recruitment_note', 'show_recruitment_note');
        });
        DB::statement('ALTER TABLE `cps` MODIFY  `show_winner_label` tinyint(4) NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE `cps` MODIFY  `show_recruitment_note` tinyint(4) NOT NULL DEFAULT 0');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('cps', function(Blueprint $table)
        {
            $table->renameColumn('show_winner_label', 'use_winner_label');
            $table->renameColumn('show_recruitment_note', 'use_recruitment_note');
        });
        DB::statement('ALTER TABLE `cps` MODIFY  `use_winner_label` tinyint(4) NOT NULL DEFAULT 2');
        DB::statement('ALTER TABLE `cps` MODIFY  `use_recruitment_note` tinyint(4) NOT NULL DEFAULT 2');
	}

}
